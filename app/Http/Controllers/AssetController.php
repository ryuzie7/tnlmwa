<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::all(); // Fetch assets
        return view('dashboard.assets.index', compact('assets')); // Return view with data
    }

    public function create()
    {
        return view('dashboard.assets.create'); // Return create form
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|unique:assets,code|string|max:255',
            'type' => 'required|string|max:255',
            'acquired_at' => 'required|date',
            'condition' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        // Create new asset
        Asset::create($validated);

        return redirect()->route('assets.index')->with('success', 'Asset added successfully!');
    }

    public function edit(Asset $asset)
    {
        return view('dashboard.assets.edit', compact('asset')); // Return edit form with asset data
    }

    public function update(Request $request, Asset $asset)
    {
        // Validate the updated data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|unique:assets,code,' . $asset->id . '|string|max:255',
            'type' => 'required|string|max:255',
            'acquired_at' => 'required|date',
            'condition' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        // Update the asset data
        $asset->update($validated);

        return redirect()->route('assets.index')->with('success', 'Asset updated successfully!');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return back()->with('success', 'Asset deleted successfully!');
    }
}
