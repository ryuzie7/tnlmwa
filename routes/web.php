<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\{
    AuthController,
    AssetController,
    LogController,
    UserController,
    DashboardController,
    ProfileController,
    SwapController,
    LocateController,
    Auth\ForgotPasswordController,
    Auth\ResetPasswordController
};
use App\Http\Middleware\RoleMiddleware;

// Redirect root to dashboard
Route::get('/', fn() => redirect('/dashboard'));

//
// Public Routes (No Auth Required)
//
Route::get('/assets/{asset}/card', [AssetController::class, 'show'])->name('assets.card.show'); // Guest asset view
Route::get('/assets/{asset}/qr', [AssetController::class, 'generateQr'])->name('assets.qr.image'); // QR (SVG)
Route::get('/assets/{asset}/qr/download', [AssetController::class, 'downloadQr'])->name('assets.qr.download'); // QR Download

//
// Auth Routes
//
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//
// Password Reset
//
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('throttle:5,1')->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.store');

//
// Email Verification
//
Route::get('/email/verify', fn() => view('auth.verify-email'))->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('info', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

//
// Authenticated User Dashboard
//
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Assets
    Route::resource('assets', AssetController::class)->except(['edit', 'update']);
    Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
    Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
    Route::post('/assets/toggle-view', [AssetController::class, 'toggleView'])->name('assets.toggle-view');
    Route::get('/assets/export/csv', [AssetController::class, 'exportCsv'])->name('assets.export.csv');

    // Logs
    Route::resource('logs', LogController::class)->except(['create', 'store', 'show']);
    Route::post('/logs/{log}/restore', [LogController::class, 'restore'])->name('logs.restore');
    Route::post('/logs/{log}/approve', [LogController::class, 'approve'])->name('logs.approve');
    Route::post('/logs/{log}/reject', [LogController::class, 'reject'])->name('logs.reject');
    Route::get('/logs/requests/my', [AssetController::class, 'myRequests'])->name('logs.user.requests');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    // Asset Swap & Locate
    Route::get('/swap-location', [SwapController::class, 'index'])->name('swap.index');
    Route::post('/swap-location', [SwapController::class, 'swap'])->name('swap.perform');
    Route::post('/swap-location-from-dashboard', [SwapController::class, 'swapFromDashboard'])->name('swap.from.dashboard');

    Route::get('/locate-redirect', [LocateController::class, 'redirectToMaps'])->name('locate.redirect');
});

//
// Admin-Only Routes
//
Route::middleware(['auth', RoleMiddleware::class . ':admin'])->prefix('dashboard')->group(function () {
    // User management
    Route::post('/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Asset request approvals
    Route::get('/logs/requests', [AssetController::class, 'requests'])->name('logs.requests');
    Route::post('/logs/requests/{request}/approve', [AssetController::class, 'approveRequest'])->name('logs.requests.approve');
    Route::post('/logs/requests/{request}/reject', [AssetController::class, 'rejectRequest'])->name('logs.requests.reject');
});
