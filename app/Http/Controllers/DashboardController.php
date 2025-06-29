<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\User;
use App\Models\Log;
use App\Models\LogRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
   public function index()
{
    $assetCount = Asset::count();
    $usersCount = User::count();

    $recentUsageCount = Log::where('created_at', '>=', now()->subDays(30))->count()
        + LogRequest::where('status', 'approved')
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();

    $attentionCount = Asset::whereIn('condition', ['Fair', 'Poor'])->count();

    $pendingRequestsCount = 0;
    $myRequestsCount = 0;

    if (Auth::user()->role === 'admin') {
        $pendingRequestsCount = LogRequest::where('status', 'pending')->count();
    } else {
        $myRequestsCount = LogRequest::where('user_id', Auth::id())->count();
    }

    $logs = Log::with(['asset', 'user'])->latest()->take(5)->get();
    $requests = LogRequest::with(['asset', 'user'])
        ->where('status', 'approved')
        ->latest()
        ->take(5)
        ->get();

    $recentActivities = $logs->merge($requests)->sortByDesc(function ($entry) {
        return $entry->created_at ?? $entry->updated_at;
    })->take(5)->values();

    $locations = Asset::select('id', 'brand', 'model', 'location', 'latitude', 'longitude')
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get();

    $swappableAssets = Asset::select('id', 'brand', 'model', 'location')
        ->orderBy('location')
        ->get();

    return view('welcome', compact(
        'assetCount',
        'usersCount',
        'recentUsageCount',
        'attentionCount',
        'pendingRequestsCount',
        'myRequestsCount',
        'recentActivities',
        'locations',
        'swappableAssets'
    ));
}

}
