<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\UsageHistoryController;
use App\Http\Controllers\CustodianController;


Route::get('/', function () {
    return view('welcome'); // Homepage (same as dashboard)
})->name('dashboard'); // Fix the dashboard route

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (Requires Authentication)
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('welcome'); // Dashboard view
    })->name('dashboard');

    // Asset Management
    Route::get('/dashboard/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::get('/dashboard/assets/create', [AssetController::class, 'create'])->name('assets.create');
    Route::post('/dashboard/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::get('/dashboard/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
    Route::put('/dashboard/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
    Route::delete('/dashboard/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');

    // Usage History Routes
    Route::get('/dashboard/usagehistory', [UsageHistoryController::class, 'index'])->name('usagehistory.index');
    Route::get('/dashboard/usagehistory/create', [UsageHistoryController::class, 'create'])->name('usagehistory.create');
    Route::post('/dashboard/usagehistory', [UsageHistoryController::class, 'store'])->name('usagehistory.store');
    Route::delete('/dashboard/usagehistory/{usagehistory}', [UsageHistoryController::class, 'destroy'])->name('usagehistory.destroy');

    // Custodian Routes
    Route::get('/dashboard/custodians', [CustodianController::class, 'index'])->name('custodians.index');
    Route::get('/dashboard/custodians/create', [CustodianController::class, 'create'])->name('custodians.create');
    Route::post('/dashboard/custodians', [CustodianController::class, 'store'])->name('custodians.store');
    Route::get('/dashboard/custodians/{custodian}/edit', [CustodianController::class, 'edit'])->name('custodians.edit');
    Route::put('/dashboard/custodians/{custodian}', [CustodianController::class, 'update'])->name('custodians.update');
    Route::delete('/dashboard/custodians/{custodian}', [CustodianController::class, 'destroy'])->name('custodians.destroy');

});
