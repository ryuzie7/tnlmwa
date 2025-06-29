@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<!-- Main Welcome Card -->
<div class="bg-white dark:bg-gray-900 rounded-xl shadow hover:shadow-md transition-shadow duration-300 w-full">
    <div class="p-6 bg-gradient-to-r from-indigo-100 dark:from-gray-800 to-white dark:to-gray-900 rounded-t-xl">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">
            Teaching and Learning UiTM Inventory Management Web Application
        </h2>
        <p class="text-gray-500 dark:text-gray-300 mt-1">
            Track and manage all UiTM's Teaching and Learning assets
        </p>
    </div>

    <div class="p-6">
        <p class="text-gray-600 dark:text-gray-400 mb-5">
            Select a module below to manage your inventory resources.
        </p>

        <!-- Swap & Locate Section -->
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                <!-- Swap Location Card -->
                <form method="POST" action="{{ route('swap.from.dashboard') }}" class="h-full">
                    @csrf
                    <div class="h-full flex flex-col justify-between bg-yellow-100 dark:bg-yellow-900 border border-yellow-300 dark:border-yellow-600 rounded-xl p-6 hover:bg-yellow-200 dark:hover:bg-yellow-800 hover:border-yellow-400 dark:hover:border-yellow-500 hover:shadow transition-all">
                        <div class="flex flex-col items-center text-center mb-4">
                            <div class="icon-circle bg-yellow-300 dark:bg-yellow-800 p-3 rounded-full mb-3">
                                <i class="fas fa-exchange-alt text-2xl text-yellow-800 dark:text-yellow-300"></i>
                            </div>
                            <span class="font-medium text-lg text-yellow-900 dark:text-white">Swap Location</span>
                        </div>
                        <div class="flex flex-col gap-3">
                            <select name="asset1_id" class="w-full text-sm rounded p-2 bg-white dark:bg-gray-800 border dark:border-gray-700 text-gray-800 dark:text-white" required>
                                <option value="">-- Select Asset 1 --</option>
                                @foreach($locations as $asset)
                                    <option value="{{ $asset->id }}">{{ $asset->brand }} {{ $asset->model }} ({{ $asset->location }})</option>
                                @endforeach
                            </select>

                            <select name="asset2_id" class="w-full text-sm rounded p-2 bg-white dark:bg-gray-800 border dark:border-gray-700 text-gray-800 dark:text-white" required>
                                <option value="">-- Select Asset 2 --</option>
                                @foreach($locations as $asset)
                                    <option value="{{ $asset->id }}">{{ $asset->brand }} {{ $asset->model }} ({{ $asset->location }})</option>
                                @endforeach
                            </select>

                            <textarea name="notes" placeholder="Optional notes..." class="w-full min-h-[80px] text-sm rounded p-2 bg-white dark:bg-gray-800 border dark:border-gray-700 text-gray-800 dark:text-white"></textarea>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded shadow">
                                Confirm Swap
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Locate Asset/Location Card -->
                <form method="GET" action="{{ route('locate.redirect') }}" class="h-full">
                    <div class="h-full flex flex-col justify-between bg-blue-100 dark:bg-blue-900 border border-blue-300 dark:border-blue-600 rounded-xl p-6 hover:bg-blue-200 dark:hover:bg-blue-800 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow transition-all">
                        <div class="flex flex-col items-center text-center mb-4">
                            <div class="icon-circle bg-blue-300 dark:bg-blue-800 p-3 rounded-full mb-3">
                                <i class="fas fa-map-marker-alt text-2xl text-blue-800 dark:text-blue-300"></i>
                            </div>
                            <span class="font-medium text-lg text-blue-900 dark:text-white">Locate Asset / Location</span>
                        </div>
                        <div class="flex flex-col gap-3">
                            <select name="target" class="w-full text-sm rounded p-2 bg-white dark:bg-gray-800 border dark:border-gray-700 text-gray-800 dark:text-white" required>
                                <option value="">-- Select an item --</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->brand }} {{ $location->model }}|{{ $location->latitude }},{{ $location->longitude }}">
                                        {{ $location->brand }} {{ $location->model }} ({{ $location->location }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                                Open in Google Maps
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Action Buttons Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Assets Button -->
            <a href="{{ route('assets.index') }}" class="module-card bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 text-center hover:bg-indigo-50 dark:hover:bg-indigo-900 hover:border-indigo-200 dark:hover:border-indigo-500 hover:shadow transition-all flex flex-col items-center justify-center">
                <div class="icon-circle bg-indigo-100 dark:bg-indigo-800 p-3 rounded-full mb-3">
                    <i class="fas fa-laptop text-2xl text-indigo-600 dark:text-indigo-300"></i>
                </div>
                <span class="font-medium text-lg text-gray-800 dark:text-white">Assets</span>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Manage all physical inventory</p>
            </a>
            <!-- Users Button -->
            <a href="{{ route('users.index') }}" class="module-card bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 text-center hover:bg-green-50 dark:hover:bg-green-900 hover:border-green-200 dark:hover:border-green-500 hover:shadow transition-all flex flex-col items-center justify-center">
                <div class="icon-circle bg-green-100 dark:bg-green-800 p-3 rounded-full mb-3">
                    <i class="fas fa-users text-2xl text-green-600 dark:text-green-300"></i>
                </div>
                <span class="font-medium text-lg text-gray-800 dark:text-white">Users</span>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Register and manage asset handlers</p>
            </a>

            <!-- Logs Button -->
            <a href="{{ route('logs.index') }}" class="module-card bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 text-center hover:bg-purple-50 dark:hover:bg-purple-900 hover:border-purple-200 dark:hover:border-purple-500 hover:shadow transition-all flex flex-col items-center justify-center">
                <div class="icon-circle bg-purple-100 dark:bg-purple-800 p-3 rounded-full mb-3">
                    <i class="fas fa-history text-2xl text-purple-600 dark:text-purple-300"></i>
                </div>
                <span class="font-medium text-lg text-gray-800 dark:text-white">Logs</span>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Track asset location change requests</p>
            </a>
        </div>
    </div>
</div>
<!-- Quick Stats Section -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mt-6">
    <!-- Total Assets -->
    <a href="{{ route('assets.index') }}" class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 hover:shadow-md transition duration-300 block">
        <div class="flex items-center">
            <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full mr-4">
                <i class="fas fa-laptop text-blue-600 dark:text-blue-300"></i>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300">Total Assets</h3>
                <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $assetCount ?? '0' }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-400">All registered equipment</p>
            </div>
        </div>
    </a>

    <!-- Users -->
    <a href="{{ route('users.index') }}" class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 hover:shadow-md transition duration-300 block">
        <div class="flex items-center">
            <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full mr-4">
                <i class="fas fa-users text-green-600 dark:text-green-300"></i>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300">Users</h3>
                <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $usersCount ?? '0' }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-400">Registered asset handlers</p>
            </div>
        </div>
    </a>
    <!-- Recent Logs -->
    <a href="{{ route('logs.index') }}" class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 hover:shadow-md transition duration-300 block">
        <div class="flex items-center">
            <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-full mr-4">
                <i class="fas fa-exchange-alt text-purple-600 dark:text-purple-300"></i>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300">Recent Logs</h3>
                <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $recentUsageCount ?? '0' }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-400">Last 30 days activity</p>
            </div>
        </div>
    </a>

    <!-- Needs Attention -->
    <a href="{{ route('assets.index', ['condition' => 'attention']) }}" class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 hover:shadow-md transition duration-300 block">
        <div class="flex items-center">
            <div class="bg-red-100 dark:bg-red-900 p-3 rounded-full mr-4">
                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-300"></i>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300">Needs Attention</h3>
                <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $attentionCount ?? '0' }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-400">Maintenance required</p>
            </div>
        </div>
    </a>
    <!-- Dynamic 5th Box -->
    @if(auth()->user()->role === 'admin')
    <a href="{{ route('logs.requests') }}" class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 hover:shadow-md transition duration-300 block">
        <div class="flex items-center">
            <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-full mr-4">
                <i class="fas fa-hourglass-half text-yellow-600 dark:text-yellow-300"></i>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300">Pending Requests</h3>
                <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $pendingRequestsCount ?? '0' }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-400">Awaiting admin approval</p>
            </div>
        </div>
    </a>
    @else
    <a href="{{ route('logs.user.requests') }}" class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 hover:shadow-md transition duration-300 block">
        <div class="flex items-center">
            <div class="bg-indigo-100 dark:bg-indigo-900 p-3 rounded-full mr-4">
                <i class="fas fa-clock text-indigo-600 dark:text-indigo-300"></i>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-300">My Requests</h3>
                <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $myRequestsCount ?? '0' }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-400">Your submitted changes</p>
            </div>
        </div>
    </a>
    @endif
</div>
<!-- Recent Activity Table -->
<div class="bg-white dark:bg-gray-900 rounded-xl shadow mt-6 overflow-hidden">
    <div class="p-6 bg-gradient-to-r from-indigo-100 dark:from-gray-800 to-white dark:to-gray-900 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Recent Activity</h2>
        <a href="{{ route('logs.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 flex items-center">
            View All <i class="fas fa-arrow-right ml-1 text-xs"></i>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date & Time</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Asset</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Details</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($recentActivities ?? [] as $activity)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-4 py-3">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ optional($activity->created_at)->format('Y-m-d') ?? 'N/A' }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ optional($activity->created_at)->format('H:i') ?? '' }}
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center">
                            <div class="rounded-full bg-indigo-100 dark:bg-indigo-800 p-2 mr-2">
                                <i class="fas fa-laptop text-indigo-500 dark:text-indigo-300 text-xs"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ optional($activity->asset)->brand ?? 'N/A' }} {{ optional($activity->asset)->model ?? '' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center">
                            <div class="rounded-full bg-green-100 dark:bg-green-800 p-2 mr-2">
                                <i class="fas fa-user text-green-500 dark:text-green-300 text-xs"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ optional($activity->user)->name ?? 'N/A' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            {{ $activity->action ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="#" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-medium">
                            View <i class="fas fa-chevron-right ml-1 text-xs"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col items-center">
                            <div class="rounded-full bg-gray-100 dark:bg-gray-800 p-3 mb-3">
                                <i class="fas fa-clipboard-list text-gray-400 dark:text-gray-500 text-lg"></i>
                            </div>
                            <p class="font-medium">No recent activities</p>
                            <p class="text-sm text-gray-400">Activities will appear here when assets are used</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
