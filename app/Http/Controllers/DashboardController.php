<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $assets = Asset::with([
            'custodian',
            'usageHistories' => function ($query) {
                $query->latest()->limit(1); // fetch latest usage record
            }
        ])->get();

        return view('welcome', compact('assets'));
    }
}
