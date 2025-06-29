<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocateController extends Controller
{
    public function redirectToMaps(Request $request)
    {
        $target = $request->get('target');

        // Ensure target is provided and properly formatted
        if (!$target || !str_contains($target, '|')) {
            return redirect()->back()->with('error', '⚠️ Invalid selection format.');
        }

        [$label, $coordinates] = explode('|', $target);

        // Trim and validate coordinates (must match latitude,longitude)
        $coordinates = trim($coordinates);
        if (!preg_match('/^-?\d+(\.\d+)?,-?\d+(\.\d+)?$/', $coordinates)) {
            return redirect()->back()->with('error', '⚠️ Invalid coordinate format.');
        }

        // Redirect user to Google Maps
        $googleMapsUrl = "https://www.google.com/maps/search/?api=1&query=" . urlencode($coordinates);
        return redirect()->away($googleMapsUrl);
    }
}
