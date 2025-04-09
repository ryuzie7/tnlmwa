<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\UsageHistory; // This is assuming you have the 'UsageHistory' model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsageHistoryController extends Controller // Class name should match the filename
{
    public function index()
    {
        $usages = UsageHistory::with(['asset', 'user'])->get();  // Assuming 'user' and 'asset' are related properly
        return view('dashboard.usage.index', compact('usages'));
    }

    public function create()
    {
        $assets = Asset::all();  // Fetch assets for the usage form
        return view('dashboard.usage.create', compact('assets'));
    }

    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'purpose' => 'required|string',
            'used_at' => 'required|date',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Create a new usage history entry
        UsageHistory::create([
            'asset_id' => $validated['asset_id'],
            'user_id' => $user->id,  // Assuming 'user_id' for custodian
            'purpose' => $validated['purpose'],
            'used_at' => $validated['used_at'],
        ]);

        return redirect()->route('usagehistory.index')->with('success', 'Usage recorded.');
    }

    public function destroy(UsageHistory $usage)
    {
        // Delete usage history
        $usage->delete();

        return back()->with('success', 'Usage history deleted.');
    }
}
