@extends('layouts.app')

@section('title', 'Asset Logs')

@section('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
@endsection

@section('content')
@php
    $previousUrl = url()->previous();
    $isSamePage = $previousUrl === url()->current();
    $backUrl = $isSamePage ? route('dashboard') : $previousUrl;
@endphp

<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex flex-col gap-3 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Asset Logs</h2>
            <a href="{{ $backUrl }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-700 rounded hover:bg-gray-600 shadow transition">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>

        <div class="flex flex-wrap justify-end gap-3">
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('logs.requests') }}"
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg shadow-sm flex items-center gap-2">
                    <i class="fas fa-envelope-open-text"></i> View Asset Requests
                </a>
            @else
                <a href="{{ route('logs.user.requests') }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-sm flex items-center gap-2">
                    <i class="fas fa-list-alt"></i> My Requests
                </a>
            @endif

            <form method="GET" action="{{ route('logs.index') }}">
                <input type="hidden" name="export" value="csv">
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-sm flex items-center gap-2">
                    <i class="fas fa-file-export"></i> Export CSV
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-100 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-100 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <form method="GET" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-1">Asset</label>
            <select name="asset" class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                <option value="">All Assets</option>
                @foreach($assets as $asset)
                    <option value="{{ $asset->id }}" {{ request('asset') == $asset->id ? 'selected' : '' }}>
                        {{ $asset->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-1">User</label>
            <select name="user" class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                <option value="">All Users</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="sm:col-span-2 lg:col-span-3 flex justify-end gap-2 mt-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            <a href="{{ route('logs.index') }}"
               class="bg-gray-300 dark:bg-gray-600 dark:text-white hover:bg-gray-400 px-4 py-2 rounded-lg shadow-sm">
                <i class="fas fa-times mr-1"></i> Clear
            </a>
        </div>
    </form>

    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow-sm">
        <table class="min-w-full text-sm border rounded-lg">
            <thead class="bg-gray-100 dark:bg-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase">
                <tr>
                    <th class="px-4 py-2">Asset</th>
                    <th class="px-4 py-2">User</th>
                    <th class="px-4 py-2">From</th>
                    <th class="px-4 py-2">To</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Requested At</th>
                    <th class="px-4 py-2">Applied At</th>
                    <th class="px-4 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 border-t dark:border-gray-700">
                        <td class="px-4 py-2">{{ $log->asset->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $log->user->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $log->from_location ?? $log->changes['from_location'] ?? $log->changes['asset1']['location'] ?? $log->asset->location ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $log->to_location ?? $log->changes['to_location'] ?? $log->changes['asset2']['location'] ?? $log->asset->location ?? '-' }}</td>
                        <td class="px-4 py-2">
                            @if($log->action === 'deleted')
                                <span class="bg-red-600 text-white px-2 py-1 rounded text-xs">Deleted</span>
                            @elseif($log->status === 'pending')
                                <span class="bg-yellow-300 dark:bg-yellow-500 text-black px-2 py-1 rounded text-xs">Pending</span>
                            @elseif($log->status === 'approved')
                                <span class="bg-green-500 text-white px-2 py-1 rounded text-xs">Approved</span>
                            @else
                                <span class="bg-red-500 text-white px-2 py-1 rounded text-xs">Rejected</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $log->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-2">{{ $log->applied_at ? $log->applied_at->format('M d, Y H:i') : '-' }}</td>
                        <td class="px-4 py-2 text-center space-x-2">
                            @if(auth()->user()->role === 'admin' && $log->status === 'pending')
                                <form action="{{ route('logs.approve', $log->id) }}" method="POST" class="inline">@csrf
                                    <button class="text-green-600 hover:text-green-800 dark:hover:text-green-400" onclick="return confirm('Approve this log?')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('logs.reject', $log->id) }}" method="POST" class="inline">@csrf
                                    <button class="text-red-600 hover:text-red-800 dark:hover:text-red-400" onclick="return confirm('Reject this log?')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            @elseif(auth()->user()->id === $log->user_id && $log->status === 'pending')
                                <a href="{{ route('logs.edit', $log->id) }}" class="text-blue-600 hover:text-blue-800 dark:hover:text-blue-400">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('logs.destroy', $log->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this log request?')">@csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 dark:hover:text-red-400">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @elseif($log->action === 'deleted' && auth()->user()->role === 'admin')
                                <form action="{{ route('logs.restore', $log->id) }}" method="POST" class="inline">@csrf
                                    <button class="text-blue-600 hover:text-blue-800 dark:hover:text-blue-400">
                                        <i class="fas fa-undo"></i> Restore
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 dark:text-gray-500">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-gray-500 dark:text-gray-300 py-6">No logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="mt-6">
            {{ $logs->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(function () {
        $('#daterange').daterangepicker({
            opens: 'left',
            showDropdowns: true,
            autoApply: true,
            locale: {
                format: 'MM/DD/YYYY'
            },
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment()
        });
    });
</script>
@endsection
