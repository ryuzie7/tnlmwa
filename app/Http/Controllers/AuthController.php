<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\UserAccessNotification;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'staff_id' => 'required|string|max:20|unique:users',
            'phone' => 'required|string|max:15',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'staff_id' => $validated['staff_id'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'approved' => true,
            'role' => 'user',
        ]);

        event(new Registered($user)); // Send email verification
        Auth::login($user);

        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            Notification::route('mail', $admin->email)->notify(new UserAccessNotification($user, 'registered'));
        }

        return redirect()->route('verification.notice')->with('info', 'Please verify your email address.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.']);
        }

        if (! $user->hasVerifiedEmail()) {
            return back()->withErrors(['email' => 'Please verify your email before logging in.']);
        }

        Auth::login($user);

        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            Notification::route('mail', $admin->email)->notify(new UserAccessNotification($user, 'logged in'));
        }

        return redirect()->route('dashboard')->with('success', 'Login successful.');
    }
}
