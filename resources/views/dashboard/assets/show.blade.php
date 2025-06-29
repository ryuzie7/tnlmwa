@extends('layouts.app')

@section('title', 'Asset Details')

@section('content')
@php
    $backUrl = url()->previous() === url()->current() ? route('assets.index') : url()->previous();
@endphp

<div class="w-full px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Asset Details</h1>
        <a href="{{ $backUrl }}"
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-600 rounded shadow">
            <i class="fas fa-arrow-left mr-2"></i> Back
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="p-6 space-y-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">{{ $asset->brand }} {{ $asset->model }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700 dark:text-gray-300">
                <div><strong>Property No:</strong> {{ $asset->property_number }}</div>
                <div><strong>Type:</strong> {{ $asset->type }}</div>
                <div><strong>Condition:</strong>
                    <span class="inline-block px-2 py-1 rounded text-xs font-semibold
                        {{ $asset->condition === 'Fair' ? 'bg-yellow-100 dark:bg-yellow-300 text-yellow-800' :
                           ($asset->condition === 'Poor' ? 'bg-red-100 dark:bg-red-500 text-red-800 dark:text-white' : 'bg-green-100 dark:bg-green-400 text-green-800') }}">
                        {{ $asset->condition }}
                    </span>
                </div>
                <div><strong>Location:</strong> {{ $asset->location }}</div>
                <div><strong>Building:</strong> {{ $asset->building_name }}</div>
                <div><strong>Fund:</strong> {{ $asset->fund }}</div>
                <div><strong>Price (RM):</strong> {{ $asset->price !== null ? number_format($asset->price, 2) : '-' }}</div>
                <div><strong>Year Acquired:</strong> {{ $asset->acquired_at ?? '-' }}</div>
                <div><strong>Previous Custodian:</strong> {{ $asset->previous_custodian }}</div>
                <div><strong>Current Custodian:</strong> {{ $asset->custodian }}</div>
                <div><strong>Notes:</strong> {{ $asset->notes ?? '-' }}</div>
            </div>

            @if($asset->latitude && $asset->longitude)
            <div class="pt-4">
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $asset->latitude }},{{ $asset->longitude }}"
                   target="_blank"
                   class="text-indigo-600 hover:text-indigo-800 dark:hover:text-indigo-400 text-sm inline-flex items-center gap-1">
                    <i class="fas fa-location-arrow"></i> Get Directions
                </a>
            </div>
            @endif
        </div>

        <div class="px-6 pb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-t dark:border-gray-700 mt-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('assets.qr.download', $asset) }}" class="text-gray-700 dark:text-gray-300 hover:text-black dark:hover:text-white text-sm inline-flex items-center gap-1">
                    <i class="fas fa-qrcode"></i> Download QR
                </a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('assets.edit', $asset) }}" class="text-blue-600 hover:text-blue-800 dark:hover:text-blue-400 text-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('Delete this asset?')" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 dark:hover:text-red-400 text-sm">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
