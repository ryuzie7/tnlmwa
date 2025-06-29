<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\LogRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class SwapController extends Controller
{
    public function swapFromDashboard(Request $request)
    {
        $request->validate([
            'asset1_id' => 'required|different:asset2_id|exists:assets,id',
            'asset2_id' => 'required|exists:assets,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $asset1 = Asset::findOrFail($request->asset1_id);
        $asset2 = Asset::findOrFail($request->asset2_id);

        $originalLocation1 = $asset1->location;
        $originalLocation2 = $asset2->location;

        $swapGroupId = Str::uuid();
        $now = now();

        if (Auth::user()->role === 'admin') {
            // Swap locations immediately
            $asset1->update(['location' => $originalLocation2]);
            $asset2->update(['location' => $originalLocation1]);

            // Create approved logs
            $this->createLogRequest($asset1, $originalLocation1, $originalLocation2, $request->notes, 'approved', $swapGroupId, $now);
            $this->createLogRequest($asset2, $originalLocation2, $originalLocation1, $request->notes, 'approved', $swapGroupId, $now);

            return Redirect::route('dashboard')->with('success', 'Assets have been swapped successfully.');
        } else {
            // Request for approval
            $this->createLogRequest($asset1, $originalLocation1, $originalLocation2, $request->notes, 'pending', $swapGroupId, $now);
            $this->createLogRequest($asset2, $originalLocation2, $originalLocation1, $request->notes, 'pending', $swapGroupId, $now);

            return Redirect::route('dashboard')->with('success', 'Swap request has been submitted for admin approval.');
        }
    }

    private function createLogRequest($asset, $originalLocation, $newLocation, $notes, $status, $groupId, $timestamp)
    {
        LogRequest::create([
            'asset_id' => $asset->id,
            'user_id' => Auth::id(),
            'date' => $timestamp->toDateString(),
            'usage_type' => 'location_swap',
            'notes' => $notes,
            'original_location' => $originalLocation,
            'new_location' => $newLocation,
            'brand' => $asset->brand,
            'model' => $asset->model,
            'action' => 'update',
            'status' => $status,
            'requested_at' => $timestamp,
            'reviewed_at' => $status === 'approved' ? $timestamp : null,
            'applied_at' => $status === 'approved' ? $timestamp : null,
            'swap_group_id' => $groupId,
        ]);
    }
}
