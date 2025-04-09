<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Show the login form
    public function showLogin()
    {
        return view('auth.login');
    }

public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login'); // or any page you want
}


    // Show the registration form
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle the registration request
    public function register(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed', // Ensure confirmation rule is applied
            'staff_id' => 'required|string|max:20|unique:users',
            'phone' => 'required|string|max:15',
        ]);

        try {
            // Create new user in the database
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'staff_id' => $validated['staff_id'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']), // Hash the password for security
                'approved' => true, // Assuming users are automatically approved
            ]);

            // Log the user in immediately after registration
            Auth::login($user);

            // Redirect to the dashboard with a success message
            return redirect()->route('dashboard')->with('success', 'Account created successfully! You are now logged in.');

        } catch (\Exception $e) {
            // Log the exception message for debugging
            Log::error('Registration failed: ' . $e->getMessage());

            // Handle errors gracefully and provide a useful error message
            return back()->withErrors(['error' => 'Something went wrong while creating your account. Please try again.']);
        }
    }

    // Handle login request
    public function login(Request $request)
    {
        // Validate login input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check if user exists in the database
        $user = User::where('email', $request->email)->first();

        // Verify password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.']);
        }

        // Log the user in
        Auth::login($user);

        // Redirect to the dashboard after successful login
        return redirect()->route('dashboard')->with('success', 'Login successful.');
    }
}
