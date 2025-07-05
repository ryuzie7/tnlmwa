<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\Log;
use App\Models\LogRequest;
use App\Models\User;
use App\Notifications\AssetRequestSubmitted;
use App\Notifications\AssetRequestStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $cacheKey = 'assets_index_' . md5(json_encode($request->query()));

        $assets = Cache::remember($cacheKey, 60, function () use ($request) {
            $query = Asset::with(['user', 'logs'])
                ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                    $q->where('property_number', 'like', '%' . $request->search . '%')
                      ->orWhere('brand', 'like', '%' . $request->search . '%')
                      ->orWhere('model', 'like', '%' . $request->search . '%');
                }))
                ->when($request->type, fn($q) => $q->where('type', $request->type))
                ->when($request->condition === 'attention', fn($q) => $q->whereIn('condition', ['Fair', 'Poor']),
                    fn($q) => $request->condition ? $q->where('condition', $request->condition) : $q)
                ->when($request->location, fn($q) => $q->where('location', $request->location));

            if ($request->filled('sort')) {
                $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';
                $query->orderBy($request->input('sort'), $direction);
            } else {
                $query->latest('created_at');
            }

            return $query->paginate($request->get('per_page', 10))
                         ->appends($request->query());
        });

        $viewType = $request->get('view_type', session('asset_view', 'card'));
        session(['asset_view' => $viewType]);

        $types = Cache::remember('asset_types', 3600, fn() => Asset::distinct()->pluck('type')->toArray());
        $conditions = ['Good', 'Fair', 'Poor'];
        $locations = Cache::remember('asset_locations', 3600, fn() => Asset::distinct()->pluck('location')->toArray());

        $view = $viewType === 'card'
            ? 'dashboard.assets.card'
            : 'dashboard.assets.list';

        return view($view, compact('assets', 'viewType', 'types', 'conditions', 'locations'));
    }

    public function toggleView(Request $request)
    {
        $view = $request->input('view', 'card');
        session(['asset_view' => $view]);

        return $request->wantsJson()
            ? response()->json(['success' => true, 'view' => $view])
            : back();
    }

    public function create()
    {
        return view('dashboard.assets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'type' => 'required|string|max:255',
            'condition' => 'required|in:Good,Fair,Poor',
            'location' => 'nullable|string|max:255',
            'building_name' => 'nullable|string|max:255',
            'fund' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'previous_custodian' => 'nullable|string|max:255',
            'custodian' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if (Auth::user()->role === 'admin') {
            $year = now()->year;
            $validated['acquired_at'] = $year;

            $lastAsset = Asset::where('property_number', 'like', $year . '%')
                ->orderBy('property_number', 'desc')
                ->first();

            $lastCounter = $lastAsset ? (int)substr($lastAsset->property_number, -6) : 0;
            $propertyNumber = $year . str_pad($lastCounter + 1, 6, '0', STR_PAD_LEFT);

            $validated['property_number'] = $propertyNumber;
            $asset = Asset::create($validated);

            Log::create([
                'asset_id' => $asset->id,
                'action' => 'created',
                'model_type' => 'Asset',
                'model_id' => $asset->id,
                'changes' => $asset->toArray(),
                'user_id' => auth()->id(),
            ]);

            Cache::forget('asset_types');
            Cache::forget('asset_locations');

            return redirect()->route('assets.index')->with('success', 'Asset added successfully!');
        }

        $assetRequest = AssetRequest::create(array_merge($validated, [
            'user_id' => auth()->id(),
            'action' => 'create',
            'status' => 'pending',
            'requested_at' => now(),
        ]));

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new AssetRequestSubmitted($assetRequest));

        return redirect()->route('assets.index')->with('info', 'Asset creation request submitted for admin approval.');
    }

    public function edit(Asset $asset)
    {
        return view('dashboard.assets.edit', compact('asset'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'property_number' => 'required|string|max:255|unique:assets,property_number,' . $asset->id,
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'type' => 'required|string|max:255',
            'condition' => 'required|in:Good,Fair,Poor',
            'location' => 'nullable|string|max:255',
            'building_name' => 'nullable|string|max:255',
            'fund' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'acquired_at' => 'nullable|integer|min:1900|max:' . now()->year,
            'previous_custodian' => 'nullable|string|max:255',
            'custodian' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if (auth()->user()->role === 'admin') {
            $original = $asset->getOriginal();
            $asset->update($validated);

            Log::create([
                'asset_id' => $asset->id,
                'action' => 'updated',
                'model_type' => 'Asset',
                'model_id' => $asset->id,
                'changes' => ['before' => $original, 'after' => $asset->getChanges()],
                'user_id' => auth()->id(),
            ]);

            Cache::forget('asset_types');
            Cache::forget('asset_locations');

            return redirect()->route('assets.index')->with('success', 'Asset updated successfully!');
        }

        $assetRequest = AssetRequest::create([
            'user_id' => auth()->id(),
            'action' => 'edit',
            'status' => 'pending',
            'requested_at' => now(),
            'original_data' => $asset->toArray(),
            ...$validated,
        ]);

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new AssetRequestSubmitted($assetRequest));

        return redirect()->route('assets.index')->with('info', 'Asset edit request submitted for admin approval.');
    }

    public function destroy(Asset $asset)
    {
        $original = $asset->toArray();

        Log::create([
            'asset_id' => $asset->id,
            'action' => 'deleted',
            'model_type' => 'Asset',
            'model_id' => $asset->id,
            'changes' => $original,
            'user_id' => auth()->id(),
            'date' => now(),
            'usage_type' => 'deletion',
            'notes' => 'Asset permanently deleted.',
        ]);

        $asset->delete();

        Cache::forget('asset_types');
        Cache::forget('asset_locations');

        return back()->with('success', 'Asset deleted and logged successfully!');
    }

    public function show(Asset $asset)
    {
        return view('dashboard.assets.show', compact('asset'));
    }

    public function generateQr(Asset $asset)
    {
        $url = route('assets.card.show', $asset->id);
        return response(QrCode::format('svg')->size(200)->generate($url), 200, [
            'Content-Type' => 'image/svg+xml',
        ]);
    }

public function qrImage(Asset $asset)
{
    $url = route('assets.card.show', $asset->id);
    $qr = QrCode::format('png')->size(200)->generate($url);

    return response($qr)->header('Content-Type', 'image/png');
}



public function downloadQr(Asset $asset)
{
    $url = route('assets.card.show', $asset->id);
    $qrSvg = trim(QrCode::format('svg')->size(300)->margin(0)->generate($url));

    // Clean up: remove internal XML declarations from QR SVG
    $qrSvg = preg_replace('/<\?xml.*?\?>/', '', $qrSvg);

    $brand = e($asset->brand ?? '');
    $model = e($asset->model ?? '');
    $propertyNumber = e($asset->property_number);

    $filename = 'asset_qr_fullpage_' . $propertyNumber . '.svg';

    $svg = <<<SVG
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<svg width="210mm" height="297mm" viewBox="0 0 210 297" version="1.1"
     xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">

  <style>
    text {
      font-family: Arial, sans-serif;
      fill: #000;
    }
    .heading { font-size: 10px; font-weight: bold; }
    .label { font-size: 9px; }
    .footer { font-size: 8px; fill: #555; }
  </style>

  <rect width="100%" height="100%" fill="white"/>

  <!-- UiTM Logo -->
  <image x="75" y="10" width="60" height="30"
         xlink:href="https://perlis.uitm.edu.my/images/logo/UiTM_new_on_white_background.png" />

  <!-- QR Code -->
  <g transform="translate(55, 55) scale(0.3)">
    {$qrSvg}
  </g>

  <!-- Asset Info -->
  <text x="105" y="155" text-anchor="middle" class="heading">{$brand} {$model}</text>
  <text x="105" y="162" text-anchor="middle" class="label">Property No: {$propertyNumber}</text>

  <!-- Call to action -->
  <text x="105" y="175" text-anchor="middle" class="label">Scan Me for Info of This Item</text>

  <!-- URL -->
  <text x="105" y="185" text-anchor="middle" class="footer">https://teachingandlearningmwa.site</text>

</svg>
SVG;

    return response($svg)
        ->header('Content-Type', 'image/svg+xml')
        ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
}

    public function exportCsv(Request $request)
    {
        $assets = Asset::when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('property_number', 'like', '%' . $request->search . '%')
                  ->orWhere('brand', 'like', '%' . $request->search . '%')
                  ->orWhere('model', 'like', '%' . $request->search . '%');
            }))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->condition === 'attention', fn($q) => $q->whereIn('condition', ['Fair', 'Poor']),
                fn($q) => $request->condition ? $q->where('condition', $request->condition) : $q)
            ->when($request->location, fn($q) => $q->where('location', $request->location))
            ->get();

        $filename = 'assets_export_' . now()->format('Ymd_His') . '.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=$filename"];
        $columns = ['property_number', 'brand', 'model', 'type', 'condition', 'location', 'building_name', 'fund', 'price', 'acquired_at', 'previous_custodian', 'custodian'];

        $callback = function () use ($assets, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            foreach ($assets as $asset) {
                $row = [];
                foreach ($columns as $column) {
                    $row[] = $asset->{$column};
                }
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function requests(Request $request)
    {
        $assetRequests = AssetRequest::with('user')
            ->where('status', 'pending')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('brand', 'like', '%' . $request->search . '%')
                          ->orWhere('model', 'like', '%' . $request->search . '%');
                });
            })
            ->orderByDesc('requested_at')
            ->get();

        $swapRequests = LogRequest::with('asset')
            ->where('usage_type', 'location_swap')
            ->where('status', 'pending')
            ->orderBy('swap_group_id')
            ->get()
            ->groupBy('swap_group_id');

        return view('dashboard.logs.request', compact('assetRequests', 'swapRequests'));
    }

    public function approveRequest(AssetRequest $request)
    {
        $log = Log::where('model_type', 'AssetRequest')
            ->where('model_id', $request->id)
            ->latest()
            ->first();

        if ($log) {
            $log->update([
                'status' => 'approved',
                'applied_at' => now(),
                'notes' => 'Asset request approved and applied to the database.',
            ]);
        } else {
            Log::create([
                'asset_id' => $request->action === 'edit'
                    ? optional(Asset::where('property_number', $request->original_data['property_number'] ?? null)->first())->id
                    : null,
                'action' => 'approved',
                'model_type' => 'AssetRequest',
                'model_id' => $request->id,
                'changes' => [
                    'brand' => $request->brand,
                    'model' => $request->model,
                    'location' => $request->location,
                    'action_requested' => $request->action,
                    'original_data' => $request->original_data,
                    'reason' => 'Approved by admin',
                ],
                'user_id' => auth()->id(),
                'status' => 'approved',
                'applied_at' => now(),
                'notes' => 'Asset request approved and logged for record.',
            ]);
        }

        if ($request->action === 'edit') {
            $asset = Asset::where('property_number', $request->original_data['property_number'] ?? null)->first();
            if ($asset) {
                $asset->update([
                    'brand' => $request->brand,
                    'model' => $request->model,
                    'location' => $request->location,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ]);
            }
        }

        $request->update(['status' => 'approved']);
        $request->user->notify(new AssetRequestStatusNotification($request, 'approved'));

        return back()->with('success', 'Asset request approved.');
    }

    public function rejectRequest(AssetRequest $request)
    {
        $asset = $request->action === 'edit'
            ? Asset::where('property_number', $request->original_data['property_number'] ?? null)->first()
            : null;

        $existingLog = Log::where('model_type', 'AssetRequest')
            ->where('model_id', $request->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($existingLog) {
            $existingLog->update([
                'status' => 'rejected',
                'applied_at' => now(),
                'notes' => 'Asset request rejected by admin.',
            ]);
        } else {
            Log::create([
                'asset_id' => $asset?->id,
                'action' => 'rejected',
                'model_type' => 'AssetRequest',
                'model_id' => $request->id,
                'changes' => [
                    'brand' => $request->brand,
                    'model' => $request->model,
                    'location' => $request->location,
                    'action_requested' => $request->action,
                    'original_data' => $request->original_data,
                    'reason' => 'Rejected by admin',
                ],
                'user_id' => auth()->id(),
                'status' => 'rejected',
                'applied_at' => now(),
                'notes' => 'Asset request rejected and logged for record.',
            ]);
        }

        $request->update(['status' => 'rejected']);
        $request->user->notify(new AssetRequestStatusNotification($request, 'rejected'));

        return back()->with('info', 'Asset request rejected.');
    }

public function myRequests(Request $request)
{
    $status = $request->input('status');
    $search = $request->input('asset');

    $requests = AssetRequest::where('user_id', auth()->id())
        ->when($status, function ($query, $status) {
            $query->where('status', $status);
        })
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        })
        ->orderByDesc('requested_at')
        ->get();

    $logs = Log::with('asset')
        ->where('user_id', auth()->id())
        ->orderByDesc('created_at')
        ->get();

    return view('dashboard.logs.userreqview', compact('logs', 'requests'));
}

}
