<?php

namespace App\Http\Controllers;

use App\Models\{Asset, User, Log, LogRequest};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $start = null;
        $end = null;

        if ($request->daterange) {
            $dates = explode(' - ', $request->daterange);
            if (count($dates) === 2) {
                $start = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $end = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
            }
        }

        $logs = Log::with(['asset', 'user'])
            ->when($request->asset, fn($q) => $q->where('asset_id', $request->asset))
            ->when($request->user, fn($q) => $q->where('user_id', $request->user))
            ->when($start && $end, fn($q) => $q->whereBetween('date', [$start, $end]))
            ->get();

        $requests = LogRequest::with(['asset', 'user'])
            ->where('status', 'approved')
            ->when($request->asset, fn($q) => $q->where('asset_id', $request->asset))
            ->when($request->user, fn($q) => $q->where('user_id', $request->user))
            ->when($start && $end, fn($q) => $q->whereBetween('date', [$start, $end]))
            ->get();

        $allLogs = $logs->merge($requests)->sortByDesc(function ($log) {
            return $log->requested_at ?? $log->created_at;
        })->values();

        $allLogs->transform(function ($log) {
            if ($log instanceof LogRequest) {
                $log->from_location = $log->original_location;
                $log->to_location = $log->new_location;
            } elseif ($log instanceof Log) {
                if ($log->action === 'swap' && isset($log->changes['asset1']) && isset($log->changes['asset2'])) {
                    $log->from_location = $log->changes['asset1']['location'] ?? '-';
                    $log->to_location = $log->changes['asset2']['location'] ?? '-';
                } else {
                    $log->from_location = $log->changes['from_location'] ?? '-';
                    $log->to_location = $log->changes['to_location'] ?? '-';
                }
            }
            return $log;
        });

        // ✅ CSV Export if requested
        if ($request->has('export') && $request->export === 'csv') {
            $csvHeaders = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="asset_logs.csv"',
            ];

            $callback = function () use ($allLogs) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Asset', 'User', 'From', 'To', 'Status', 'Requested At', 'Applied At']);

                foreach ($allLogs as $log) {
                    fputcsv($handle, [
                        $log->asset->name ?? 'N/A',
                        $log->user->name ?? 'N/A',
                        $log->from_location ?? '-',
                        $log->to_location ?? '-',
                        ucfirst($log->status ?? 'N/A'),
                        ($log->requested_at ?? $log->created_at)?->format('Y-m-d H:i'),
                        $log->applied_at?->format('Y-m-d H:i') ?? '-',
                    ]);
                }

                fclose($handle);
            };

            return response()->stream($callback, 200, $csvHeaders);
        }

        // Paginate if not exporting
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $items = $allLogs instanceof Collection ? $allLogs : Collection::make($allLogs);
        $pagedLogs = new LengthAwarePaginator(
            $items->forPage($currentPage, $perPage),
            $items->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        $assets = Asset::all();
        $users = User::all();

        return view('dashboard.logs.index', [
            'logs' => $pagedLogs,
            'assets' => $assets,
            'users' => $users,
        ]);
    }

public function userRequests(Request $request)
{
    $query = LogRequest::with('asset')
        ->where('user_id', Auth::id());

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('asset')) {
        $term = $request->asset;
        $query->where(function ($q) use ($term) {
            $q->where('brand', 'like', "%$term%")
              ->orWhere('model', 'like', "%$term%")
              ->orWhereHas('asset', function ($qa) use ($term) {
                  $qa->where('name', 'like', "%$term%");
              });
        });
    }

    $requests = $query->orderByDesc('requested_at')->get();

    return view('dashboard.logs.userreqview', compact('requests'));
}


    public function create()
    {
        $assets = Asset::all();
        $users = User::all();
        return view('dashboard.logs.request', compact('assets', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'usage_type' => 'nullable|string|in:general,maintenance,inspection,training,other',
            'new_location' => 'required|string|max:255'
        ]);

        $asset = Asset::findOrFail($request->asset_id);

        if (Auth::user()->role === 'admin') {
            $asset->update(['location' => $request->new_location]);

            Log::create([
                'asset_id' => $request->asset_id,
                'user_id' => Auth::id(),
                'date' => $request->date,
                'notes' => $request->notes,
                'usage_type' => $request->usage_type ?? 'general',
                'action' => 'created',
                'status' => 'approved',
                'applied_at' => now(),
            ]);

            return redirect()->route('logs.index')->with('success', 'Log record created and location updated.');
        }

        LogRequest::create([
            'asset_id' => $request->asset_id,
            'user_id' => Auth::id(),
            'date' => $request->date,
            'notes' => $request->notes,
            'usage_type' => $request->usage_type ?? 'general',
            'original_location' => $asset->location,
            'new_location' => $request->new_location,
            'action' => 'create',
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return redirect()->route('logs.index')->with('info', 'Log creation request sent for admin approval.');
    }

    public function update(Request $request, Log $log)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'usage_type' => 'nullable|string|in:general,maintenance,inspection,training,other',
            'new_location' => 'required|string|max:255'
        ]);

        $asset = Asset::findOrFail($request->asset_id);

        if (Auth::user()->role === 'admin') {
            $asset->update(['location' => $request->new_location]);

            $log->update([
                'asset_id' => $request->asset_id,
                'date' => $request->date,
                'notes' => $request->notes,
                'usage_type' => $request->usage_type ?? 'general',
                'status' => 'approved',
                'applied_at' => now(),
            ]);

            return redirect()->route('logs.index')->with('success', 'Log updated and location changed.');
        }

        LogRequest::create([
            'asset_id' => $log->asset_id,
            'user_id' => Auth::id(),
            'date' => $request->date,
            'notes' => $request->notes,
            'usage_type' => $request->usage_type ?? 'general',
            'original_location' => $asset->location,
            'new_location' => $request->new_location,
            'action' => 'update',
            'original_data' => $log->toArray(),
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return redirect()->route('logs.index')->with('info', 'Log update request sent for admin approval.');
    }

    public function destroy(Log $log)
    {
        if (Auth::user()->role === 'admin') {
            $log->delete();
            return back()->with('success', 'Log deleted successfully.');
        }

        LogRequest::create([
            'asset_id' => $log->asset_id,
            'user_id' => Auth::id(),
            'date' => $log->date,
            'notes' => $log->notes,
            'usage_type' => $log->usage_type ?? 'general',
            'original_location' => $log->asset->location,
            'new_location' => $log->asset->location,
            'action' => 'delete',
            'original_data' => $log->toArray(),
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return back()->with('info', 'Log deletion request sent for admin approval.');
    }

    public function requests()
    {
        $requests = LogRequest::with(['user', 'asset'])->latest()->paginate(10);
        return view('dashboard.logs.request', compact('requests'));
    }

    public function approve(Log $log)
    {
        if ($log->status === 'approved') {
            return back()->with('info', 'Log is already approved.');
        }

        if ($log->action === 'updated' && isset($log->changes['after'])) {
            Asset::where('id', $log->asset_id)->update($log->changes['after']);
        }

        $log->update([
            'status' => 'approved',
            'applied_at' => now(),
        ]);

        return back()->with('success', 'Log approved successfully.');
    }

    public function reject(Log $log)
    {
        $log->update([
            'status' => 'rejected',
            'applied_at' => now(),
        ]);

        return back()->with('success', 'Log rejected.');
    }

    public function restore(Log $log)
    {
        if ($log->action !== 'deleted') {
            return back()->with('error', 'Only deleted logs can be restored.');
        }

        $data = $log->changes;

        if (Asset::where('property_number', $data['property_number'])->exists()) {
            return back()->with('error', 'Asset with this property number already exists.');
        }

        $restored = Asset::create($data);

        Log::create([
            'asset_id' => $restored->id,
            'user_id' => auth()->id(),
            'action' => 'restored',
            'model_type' => Asset::class,
            'model_id' => $restored->id,
            'changes' => $data,
            'date' => now(),
            'usage_type' => 'restoration',
            'notes' => 'Asset restored from deletion.',
            'status' => 'approved',
            'applied_at' => now(),
        ]);

        return back()->with('success', 'Asset restored successfully.');
    }
}
