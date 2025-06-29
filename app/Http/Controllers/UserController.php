<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Show user list to admin.
     */
    public function index()
    {
        $users = User::where('role', 'user')->paginate(20);
        return view('dashboard.users.index', compact('users'));
    }

    /**
     * Delete user account.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'User deleted.');
    }

    /**
     * Approve user account.
     */
    public function approve(User $user)
    {
        $user->approved = true;
        $user->save();
        return back()->with('success', 'User approved.');
    }

    /**
     * Reject user account.
     */
    public function reject(User $user)
    {
        $user->approved = null;
        $user->save();
        return back()->with('info', 'User rejected.');
    }

    /**
     * Optional: Used in dashboard redirect logic
     */
    public static function checkCustodianApproval()
    {
        $user = Auth::user();

        if ($user && $user->role === 'user') {
            if ($user->approved === null) {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Your registration was rejected by the admin.',
                ]);
            }
        }

        return null;
    }
}
