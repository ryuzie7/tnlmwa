@extends('layouts.app')

@section('title', 'Assets Management')

@section('content')
<div class="w-full px-4 py-6">
    <div class="flex flex-wrap justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Asset Inventory</h1>
            <p class="text-gray-600">Manage and track all inventory assets</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('assets.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                <i class="fas fa-plus mr-1"></i> Add Asset
            </a>
            <a href="{{ route('assets.index', array_merge(request()->all(), ['view_type' => 'card'])) }}"
               class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-black rounded-md">
                <i class="fas fa-th"></i> Card View
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-x-auto mb-4">
        <form action="{{ route('assets.index') }}" method="GET" class="p-4">
            <div class="flex flex-wrap gap-4">
                <input type="text" name="search" placeholder="Search by Property No, Brand or Model"
                       value="{{ request('search') }}"
                       class="px-4 py-2 border border-gray-300 rounded w-full md:w-64" />

                <select name="type" class="px-4 py-2 border border-gray-300 rounded w-full md:w-48">
                    <option value="">All Types</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>

                <select name="condition" class="px-4 py-2 border border-gray-300 rounded w-full md:w-48">
                    <option value="">All Conditions</option>
                    @foreach($conditions as $condition)
                        <option value="{{ $condition }}" {{ request('condition') === $condition ? 'selected' : '' }}>{{ $condition }}</option>
                    @endforeach
                </select>

                <input type="text" name="location" placeholder="Location"
                       value="{{ request('location') }}"
                       class="px-4 py-2 border border-gray-300 rounded w-full md:w-48" />

                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                    Filter
                </button>

                @php
                    $isAttention = request('condition') === 'attention';
                    $attentionUrl = $isAttention
                        ? route('assets.index', array_merge(request()->except('condition')))
                        : route('assets.index', array_merge(request()->all(), ['condition' => 'attention']));
                @endphp

                <a href="{{ $attentionUrl }}"
                   class="px-4 py-2 rounded-md font-medium {{ $isAttention ? 'bg-yellow-500 text-white hover:bg-yellow-600' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    {{ $isAttention ? 'Show All Assets' : 'Need Attention' }}
                </a>
            </div>
        </form>

        <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
            <thead class="bg-gray-100 text-xs font-semibold text-gray-700 uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-3">No.</th>
                    <th class="px-4 py-3">Property No.</th>
                    <th class="px-4 py-3">Brand</th>
                    <th class="px-4 py-3">Model</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Condition</th>
                    <th class="px-4 py-3">Location</th>
                    <th class="px-4 py-3">Building</th>
                    <th class="px-4 py-3">Fund</th>
                    <th class="px-4 py-3">Price (RM)</th>
                    <th class="px-4 py-3">Year Acquired</th>
                    <th class="px-4 py-3">Previous Custodian</th>
                    <th class="px-4 py-3">Custodian</th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php $i = ($assets->currentPage() - 1) * $assets->perPage() + 1; @endphp
                @forelse($assets as $asset)
                    <tr class="{{ in_array($asset->condition, ['Fair', 'Poor']) ? 'bg-red-50' : '' }}">
                        <td class="px-4 py-2">{{ $i++ }}</td>
                        <td class="px-4 py-2">{{ $asset->property_number }}</td>
                        <td class="px-4 py-2">{{ $asset->brand }}</td>
                        <td class="px-4 py-2">{{ $asset->model }}</td>
                        <td class="px-4 py-2">{{ $asset->type }}</td>
                        <td class="px-4 py-2">{{ $asset->condition }}</td>
                        <td class="px-4 py-2">{{ $asset->location }}</td>
                        <td class="px-4 py-2">{{ $asset->building_name }}</td>
                        <td class="px-4 py-2">{{ $asset->fund }}</td>
                        <td class="px-4 py-2">{{ $asset->price !== null ? number_format($asset->price, 2) : '-' }}</td>
                        <td class="px-4 py-2">{{ $asset->acquired_at ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $asset->previous_custodian }}</td>
                        <td class="px-4 py-2">{{ $asset->custodian }}</td>
                        <td class="px-4 py-2 text-center whitespace-nowrap">
                            <a href="{{ route('assets.edit', $asset) }}" class="text-blue-600 hover:text-blue-800 mr-2">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this asset?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="text-center text-gray-500 py-6">No assets found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-6 px-4 flex justify-center">
            {{ $assets->onEachSide(1)->links('vendor.pagination.simple-gap') }}
        </div>
    </div>
</div>
@endsection
