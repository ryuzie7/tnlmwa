@extends('layouts.app')

@section('title', 'Asset List View')

@section('content')
@php
    $previousUrl = url()->previous();
    $isSamePage = $previousUrl === url()->current();
    $backUrl = $isSamePage ? route('dashboard') : $previousUrl;
@endphp

<div class="w-full px-4 py-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Asset List View</h1>

        <a href="{{ $backUrl }}"
           class="inline-flex items-center px-4 py-2 text-sm sm:text-base font-medium text-white bg-gray-700 rounded hover:bg-gray-600 shadow transition">
            <i class="fas fa-arrow-left mr-2"></i> Back
        </a>
    </div>

    <div class="flex flex-wrap justify-start sm:justify-between items-center gap-3 mb-4">
        <a href="{{ route('assets.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm text-sm transition">
            <i class="fas fa-plus mr-1"></i> Add New Asset
        </a>

        <div class="flex flex-wrap gap-2 sm:gap-3">
            <form action="{{ route('assets.export.csv') }}" method="GET">
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-sm text-sm transition">
                    <i class="fas fa-file-csv mr-1"></i> Export CSV
                </button>
            </form>

            <form action="{{ route('assets.toggle-view') }}" method="POST">
                @csrf
                <input type="hidden" name="view" value="card">
                <button type="submit"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-sm text-sm transition">
                    <i class="fas fa-th mr-1"></i> Switch to Card View
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-x-auto mb-4">
        <form action="{{ route('assets.index') }}" method="GET" class="p-4">
            <div class="flex flex-wrap gap-4">
                <input type="text" name="search"
                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded w-full md:w-64"
                       placeholder="Search by Property No, Brand or Model"
                       value="{{ request('search') }}">

                <select name="type"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded w-full md:w-48">
                    <option value="">All Types</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>

                <select name="condition"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded w-full md:w-48">
                    <option value="">All Conditions</option>
                    @foreach($conditions as $condition)
                        <option value="{{ $condition }}" {{ request('condition') === $condition ? 'selected' : '' }}>{{ $condition }}</option>
                    @endforeach
                </select>

                <input type="text" name="location"
                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded w-full md:w-48"
                       placeholder="Location"
                       value="{{ request('location') }}">

                <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
            </div>
        </form>
    </div>

    @php
        $sortable = [
            'property_number' => 'Property No.',
            'brand' => 'Brand',
            'model' => 'Model',
            'type' => 'Type',
            'condition' => 'Condition',
            'location' => 'Location',
            'building_name' => 'Building',
            'fund' => 'Fund',
            'price' => 'Price (RM)',
            'acquired_at' => 'Year Acquired',
            'previous_custodian' => 'Previous Custodian',
            'custodian' => 'Custodian',
        ];
        $currentSort = request('sort');
        $currentDirection = request('direction');
        function buildSortUrl($field) {
            $currentSort = request('sort');
            $currentDirection = request('direction');
            if ($currentSort !== $field) return array_merge(request()->except('page'), ['sort' => $field, 'direction' => 'asc']);
            if ($currentDirection === 'asc') return array_merge(request()->except('page'), ['sort' => $field, 'direction' => 'desc']);
            return request()->except(['sort', 'direction', 'page']);
        }
    @endphp

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm text-left">
            <thead class="bg-gray-100 dark:bg-gray-700 text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-3">No.</th>
                    @foreach($sortable as $field => $label)
                        <th class="px-4 py-3 whitespace-nowrap">
                            <a href="{{ route('assets.index', buildSortUrl($field)) }}"
                               class="flex items-center gap-1 hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $label }}
                                @if($currentSort === $field)
                                    @if($currentDirection === 'asc')
                                        <i class="fas fa-sort-up"></i>
                                    @elseif($currentDirection === 'desc')
                                        <i class="fas fa-sort-down"></i>
                                    @endif
                                @else
                                    <i class="fas fa-sort text-gray-400 dark:text-gray-500"></i>
                                @endif
                            </a>
                        </th>
                    @endforeach
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @php $i = ($assets->currentPage() - 1) * $assets->perPage() + 1; @endphp
                @forelse($assets as $asset)
                    <tr class="@if($asset->condition === 'Poor') bg-red-50 dark:bg-red-900 animate-pulse-slow
                               @elseif($asset->condition === 'Fair') bg-yellow-50 dark:bg-yellow-900 animate-pulse-slow
                               @else dark:bg-gray-800 @endif">
                        <td class="px-4 py-2 text-gray-900 dark:text-white @if(in_array($asset->condition, ['Fair', 'Poor'])) border-l-4 @endif
                            @if($asset->condition === 'Fair') border-yellow-500 @elseif($asset->condition === 'Poor') border-red-500 @endif">
                            {{ $i++ }}
                        </td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $asset->property_number }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $asset->brand }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $asset->model }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $asset->type }}</td>
                        <td class="px-4 py-2">
                            <span class="inline-block text-xs font-semibold px-2 py-1 rounded
                                {{ $asset->condition === 'Fair' ? 'bg-yellow-100 dark:bg-yellow-400 text-yellow-900' :
                                   ($asset->condition === 'Poor' ? 'bg-red-100 dark:bg-red-600 text-red-900 dark:text-white' : 'bg-green-100 dark:bg-green-500 text-green-900') }}">
                                {{ $asset->condition }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $asset->location }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $asset->building_name }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $asset->fund }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $asset->price !== null ? number_format($asset->price, 2) : '-' }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $asset->acquired_at ?? '-' }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $asset->previous_custodian }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $asset->custodian }}</td>
                        <td class="px-4 py-2 text-center whitespace-nowrap">
                            <a href="{{ route('assets.edit', $asset) }}"
                               class="text-blue-600 hover:text-blue-800 dark:hover:text-blue-400 mr-2">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this asset?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 dark:hover:text-red-400">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </form>
                            <div class="mt-2">
                                <a href="{{ route('assets.qr.download', $asset) }}"
                                   class="text-gray-700 dark:text-gray-300 hover:text-black dark:hover:text-white text-sm inline-flex items-center gap-1">
                                    <i class="fas fa-qrcode"></i> Download QR
                                </a>
                            </div>
                            @if($asset->latitude && $asset->longitude)
                            <div class="mt-2">
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $asset->latitude }},{{ $asset->longitude }}"
                                   target="_blank"
                                   class="text-indigo-600 hover:text-indigo-800 dark:hover:text-indigo-400 text-sm inline-flex items-center gap-1">
                                    <i class="fas fa-location-arrow"></i> Get Directions
                                </a>
                            </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="text-center text-gray-500 dark:text-gray-300 py-6">No assets found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 px-4 flex justify-center">
        {{ $assets->onEachSide(1)->links('vendor.pagination.simple-gap') }}
    </div>
</div>
@endsection

@push('styles')
<style>
    .animate-pulse-slow {
        animation: pulse 2.5s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.75; }
    }
</style>
@endpush
