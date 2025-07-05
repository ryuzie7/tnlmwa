@extends('layouts.app')

@section('title', 'My Requests')

@section('content')
@php
    $previousUrl = url()->previous();
    $isSamePage = $previousUrl === url()->current();
    $backUrl = $isSamePage ? route('dashboard') : $previousUrl;
@endphp

<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">My Asset Requests</h2>
        <a href="{{ $backUrl }}"
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-700 rounded hover:bg-gray-600 shadow transition">
            <i class="fas fa-arrow-left mr-2"></i> Back
        </a>
    </div>

    <!-- Filter Section -->
    <form method="GET" class="mb-4 flex flex-wrap gap-4 items-center">
        <select name="status" class="border rounded p-2 dark:bg-gray-700 dark:text-white">
            <option value="">All Statuses</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        <input type="text" name="asset" placeholder="Search asset..." value="{{ request('asset') }}" class="border rounded p-2 dark:bg-gray-700 dark:text-white">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Filter</button>
    </form>

    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
        @php
            $grouped = $requests->groupBy('swap_group_id');
        @endphp

<table class="min-w-full text-sm border rounded-lg mb-8">
    <thead class="bg-gray-100 dark:bg-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase">
        <tr>
            <th class="px-4 py-2">Asset</th>
            <th class="px-4 py-2">Brand</th>
            <th class="px-4 py-2">Model</th>
            <th class="px-4 py-2">From</th>
            <th class="px-4 py-2">To</th>
            <th class="px-4 py-2">Notes</th>
            <th class="px-4 py-2">Requested</th>
            <th class="px-4 py-2">Applied</th>
            <th class="px-4 py-2">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($grouped as $groupId => $group)
            @foreach ($group as $req)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 border-t dark:border-gray-700">
                    <td class="px-4 py-2">{{ $req->asset->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2">{{ $req->brand ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $req->model ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $req->original_location ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $req->new_location ?? '-' }}</td>
                    <td class="px-4 py-2 text-gray-700 dark:text-gray-300 italic">{{ $req->notes ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $req->requested_at ? $req->requested_at->format('M d, Y H:i') : '-' }}</td>
                    <td class="px-4 py-2">{{ $req->applied_at ? $req->applied_at->format('M d, Y H:i') : '-' }}</td>
                    <td class="px-4 py-2">
                        @if($req->status === 'approved')
                            <span class="bg-green-200 text-green-800 dark:bg-green-600 dark:text-white px-2 py-1 rounded text-xs font-medium">Approved</span>
                        @elseif($req->status === 'rejected')
                            <span class="bg-red-200 text-red-800 dark:bg-red-600 dark:text-white px-2 py-1 rounded text-xs font-medium">Rejected</span>
                        @else
                            <span class="bg-yellow-300 dark:bg-yellow-500 text-black px-2 py-1 rounded text-xs">Pending</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        @empty
            <tr>
                <td colspan="9" class="text-center text-gray-500 dark:text-gray-300 py-6">
                    No matching asset requests found.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

    </div>
</div>
@endsection
