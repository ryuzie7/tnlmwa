<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\Log;
use App\Models\LogRequest;
use App\Models\User;
use App\Notifications\AssetRequestSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Notifications\AssetRequestStatusNotification;

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

    public function showCard(Asset $asset)
    {
        return view('dashboard.assets.card_show', compact('asset'));
    }

    public function generateQr(Asset $asset)
{
    $url = route('assets.card.show', $asset->id);
    return response(QrCode::format('svg')->size(200)->generate($url), 200, [
        'Content-Type' => 'image/svg+xml',
    ]);
}


public function downloadQr(Asset $asset)
{
    $url = route('assets.card.show', $asset);
    $svg = QrCode::format('svg')->size(300)->generate($url);

    $filename = 'qr-' . $asset->property_number . '.svg';

    return response($svg)
        ->header('Content-Type', 'image/svg+xml')
        ->header('Content-Disposition', "attachment; filename=\"$filename\"");
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

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $columns = [
            'property_number', 'brand', 'model', 'type', 'condition', 'location', 'building_name',
            'fund', 'price', 'acquired_at', 'previous_custodian', 'custodian'
        ];

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
    // Asset create/edit requests
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

    // Swap requests
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
    // Find existing log
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
        // If no existing log, create a new one (fallback safety)
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

    // Apply changes to the asset if it's an edit
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


    // Mark the request as approved
    $request->update(['status' => 'approved']);

    // Notify user
    $request->user->notify(new AssetRequestStatusNotification($request, 'approved'));

    return back()->with('success', 'Asset request approved.');
}


   public function rejectRequest(AssetRequest $request)
{
    // Try to find the existing log for this request's asset
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
        // If no existing log, create one
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

    // Update request status
    $request->update(['status' => 'rejected']);

    // Notify user
    $request->user->notify(new AssetRequestStatusNotification($request, 'rejected'));

    return back()->with('info', 'Asset request rejected.');
}


public function myRequests()
{
    $logs = Log::with('asset')
        ->where('user_id', auth()->id())
        ->orderByDesc('created_at')
        ->get();

    $requests = AssetRequest::where('user_id', auth()->id())
        ->orderByDesc('requested_at')
        ->get();

    return view('dashboard.logs.userreqview', compact('logs', 'requests'));
}



}
