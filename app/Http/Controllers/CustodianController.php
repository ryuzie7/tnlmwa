<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CustodianController extends Controller
{
    public function index()
    {
        // Remove filtering by 'role', retrieve all users (or adjust for another condition)
        $custodians = User::all(); // Fetch all users

        return view('dashboard.custodians.index', compact('custodians'));
    }

    public function approve(User $user)
    {
        // Approve the user (you might want to check other conditions, such as 'approved')
        $user->approved = true;
        $user->save();

        return back()->with('success', 'Custodian approved successfully.');
    }

    public function destroy(User $user)
    {
        // Delete user (make sure to handle it appropriately)
        $user->delete();

        return back()->with('success', 'Custodian deleted successfully.');
    }
}
