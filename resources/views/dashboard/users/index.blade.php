@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    <div class="flex justify-end mb-4">
    <a href="{{ url()->previous() }}"
       class="inline-flex items-center text-sm px-4 py-2 border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-100 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
        <i class="fas fa-arrow-left mr-2 text-xs"></i> Back
    </a>
</div>


    <!-- Alerts -->
    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-100 px-4 py-3 rounded mb-4 shadow-sm">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-100 px-4 py-3 rounded mb-4 shadow-sm">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <!-- Filters & Search -->
    <form method="GET" class="mb-6 bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm flex flex-col sm:flex-row sm:items-end gap-4">
        <div class="w-full sm:w-1/4">
            <label class="block text-sm text-gray-700 dark:text-gray-200 mb-1">Role</label>
            <select name="role" class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white dark:border-gray-600">
                <option value="">All</option>
                <option value="admin" @selected(request('role') == 'admin')>Admin</option>
                <option value="user" @selected(request('role') == 'user')>User</option>
            </select>
        </div>

        <div class="w-full sm:w-1/4">
            <label class="block text-sm text-gray-700 dark:text-gray-200 mb-1">Status</label>
            <select name="verified" class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white dark:border-gray-600">
                <option value="">All</option>
                <option value="1" @selected(request('verified') === '1')>Verified</option>
                <option value="0" @selected(request('verified') === '0')>Not Verified</option>
            </select>
        </div>

        <div class="w-full sm:w-1/4">
            <label class="block text-sm text-gray-700 dark:text-gray-200 mb-1">Approval</label>
            <select name="approved" class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white dark:border-gray-600">
                <option value="">All</option>
                <option value="1" @selected(request('approved') === '1')>Approved</option>
                <option value="0" @selected(request('approved') === '0')>Pending</option>
            </select>
        </div>

        <div class="w-full sm:w-1/4">
            <label class="block text-sm text-gray-700 dark:text-gray-200 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, ID" class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white dark:border-gray-600">
        </div>

        <div class="sm:ml-4">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
        </div>
    </form>

    <!-- Table -->
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm text-left">
            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Phone</th>
                    <th class="px-4 py-3">Staff ID</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Role</th>
                    @if(auth()->user()->role === 'admin')
                        <th class="px-4 py-3 text-center">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-100">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ $user->phone }}</td>
                        <td class="px-4 py-3">{{ $user->staff_id }}</td>
                        <td class="px-4 py-3">
                            @if($user->email_verified_at)
                                <span class="bg-green-600 text-white text-xs px-2 py-1 rounded font-medium">Verified</span>
                            @else
                                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded font-medium">Not Verified</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 capitalize">{{ $user->role }}</td>

                        @if(auth()->user()->role === 'admin')
                        <td class="px-4 py-3 text-center space-x-2">
                            @if(!$user->approved)
                            <form action="{{ route('users.approve', $user) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800 dark:hover:text-green-400 font-medium text-sm" onclick="return confirm('Approve this user?')">
                                    <i class="fas fa-check mr-1"></i>Approve
                                </button>
                            </form>
                            <form action="{{ route('users.reject', $user) }}" method="POST" class="inline-block ml-1">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-800 dark:hover:text-red-400 font-medium text-sm" onclick="return confirm('Reject this user?')">
                                    <i class="fas fa-times mr-1"></i>Reject
                                </button>
                            </form>
                            @else
                                <span class="text-gray-500 dark:text-gray-400 text-sm">No actions available</span>
                            @endif
                        </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-6 text-gray-500 dark:text-gray-300">
                            <div class="flex flex-col items-center">
                                <div class="rounded-full bg-gray-100 dark:bg-gray-800 p-3 mb-2">
                                    <i class="fas fa-user-slash text-gray-400 dark:text-gray-500 text-lg"></i>
                                </div>
                                <p class="font-medium">No users found</p>
                                <p class="text-sm text-gray-400">Try adjusting your filter or search.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
