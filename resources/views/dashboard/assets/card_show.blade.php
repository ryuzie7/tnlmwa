@extends('layouts.app')

@section('title', 'Asset Card View')

@section('content')
@php
    $previousUrl = url()->previous();
    $isSamePage = $previousUrl === url()->current();
    $backUrl = $isSamePage ? route('dashboard') : $previousUrl;
@endphp

<div class="w-full px-4 py-6">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white mb-4">Asset Detail (Card View)</h1>

    {{-- Back Button --}}
    <div class="mb-6">
        <a href="{{ $backUrl }}"
           class="inline-flex items-center px-4 py-2 text-sm sm:text-base font-medium text-white bg-gray-700 rounded hover:bg-gray-600 shadow transition">
            <i class="fas fa-arrow-left mr-2"></i> Back
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 sm:p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm sm:text-base text-gray-800 dark:text-gray-200">
            <div class="space-y-2">
                <p><strong>Property Number:</strong> {{ $asset->property_number }}</p>
                <p><strong>Brand:</strong> {{ $asset->brand }}</p>
                <p><strong>Model:</strong> {{ $asset->model }}</p>
                <p><strong>Type:</strong> {{ $asset->type }}</p>
                <p>
                    <strong>Condition:</strong>
                    <span class="inline-block text-xs font-semibold px-2 py-1 rounded
                        {{ $asset->condition === 'Fair' ? 'bg-yellow-100 dark:bg-yellow-400 text-yellow-900' :
                           ($asset->condition === 'Poor' ? 'bg-red-100 dark:bg-red-600 text-red-900 dark:text-white' : 'bg-green-100 dark:bg-green-500 text-green-900') }}">
                        {{ $asset->condition }}
                    </span>
                </p>
                <p><strong>Location:</strong> {{ $asset->location }}</p>
            </div>

            <div class="space-y-2">
                <p><strong>Building:</strong> {{ $asset->building_name }}</p>
                <p><strong>Fund:</strong> {{ $asset->fund }}</p>
                <p><strong>Price:</strong> {{ $asset->price !== null ? 'RM ' . number_format($asset->price, 2) : '-' }}</p>
                <p><strong>Year Acquired:</strong> {{ $asset->acquired_at ?? '-' }}</p>
                <p><strong>Previous Custodian:</strong> {{ $asset->previous_custodian }}</p>
                <p><strong>Custodian:</strong> {{ $asset->custodian }}</p>
            </div>
        </div>

        @if($asset->latitude && $asset->longitude)
        <div class="mt-6 flex justify-end">
            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $asset->latitude }},{{ $asset->longitude }}"
               target="_blank"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded shadow transition">
                <i class="fas fa-location-arrow mr-2"></i> Get Directions
            </a>
        </div>
        @endif

        @auth
        <div class="mt-4 flex justify-end">
            <a href="{{ route('assets.edit', $asset) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded shadow transition">
                <i class="fas fa-edit mr-2"></i> Edit Asset
            </a>
        </div>
        @endauth
    </div>
</div>
@endsection
