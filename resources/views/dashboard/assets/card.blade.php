@extends('layouts.app')

@section('title', 'Asset Cards')

@section('content')
@php
    $previousUrl = url()->previous();
    $isSamePage = $previousUrl === url()->current();
    $backUrl = $isSamePage ? route('dashboard') : $previousUrl;
@endphp

<div class="w-full px-4 py-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">Asset Cards View</h1>

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
                <input type="hidden" name="view" value="list">
                <button type="submit"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-sm text-sm transition">
                    <i class="fas fa-table mr-1"></i> Switch to List View
                </button>
            </form>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-6">
        {{-- Filter Panel --}}
        <div class="md:w-1/4 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
            <form action="{{ route('assets.index') }}" method="GET" class="space-y-4">
                <input type="text" name="search"
                       class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
                       placeholder="Search by Property No, Brand or Model"
                       value="{{ request('search') }}">

                <select name="type"
                        class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                    <option value="">All Types</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>

                <select name="condition"
                        class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                    <option value="">All Conditions</option>
                    @foreach($conditions as $condition)
                        <option value="{{ $condition }}" {{ request('condition') === $condition ? 'selected' : '' }}>{{ $condition }}</option>
                    @endforeach
                </select>

                <input type="text" name="location"
                       class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
                       placeholder="Location"
                       value="{{ request('location') }}">

                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm text-sm">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>

                @php
                    $isAttention = request('condition') === 'attention';
                    $attentionUrl = $isAttention
                        ? route('assets.index', array_merge(request()->except('condition')))
                        : route('assets.index', array_merge(request()->all(), ['condition' => 'attention']));
                @endphp

                <a href="{{ $attentionUrl }}"
                   class="block text-center px-4 py-2 rounded-lg font-medium text-sm shadow-sm transition-all
                   {{ $isAttention ? 'bg-yellow-500 text-white hover:bg-yellow-600' : 'bg-yellow-100 dark:bg-yellow-300 text-yellow-700 hover:bg-yellow-200' }}">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    {{ $isAttention ? 'Show All Assets' : 'Need Attention' }}
                </a>
            </form>
        </div>

        {{-- Asset Cards --}}
        <div class="md:w-3/4 grid gap-6">
            @forelse($assets as $asset)
                <div class="rounded-lg shadow-md overflow-hidden flex flex-col md:flex-row transition-shadow duration-300
                    {{ in_array($asset->condition, ['Fair', 'Poor']) ? 'bg-red-50 dark:bg-red-900 border-l-4 border-red-500 animate-pulse-slow' : 'bg-white dark:bg-gray-800' }}">
                    <div class="p-4 flex-1">
                        <div class="mb-2">
                            <h3 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-white">{{ $asset->brand }} {{ $asset->model }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Property No: {{ $asset->property_number }}</p>
                        </div>
                        <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                            <li><strong>Type:</strong> {{ $asset->type }}</li>
                            <li><strong>Condition:</strong>
                                <span class="inline-block text-xs font-semibold px-2 py-1 rounded
                                    {{ $asset->condition === 'Fair' ? 'bg-yellow-100 dark:bg-yellow-300 text-yellow-800' :
                                       ($asset->condition === 'Poor' ? 'bg-red-100 dark:bg-red-500 text-red-800 dark:text-white' : 'bg-green-100 dark:bg-green-400 text-green-800') }}">
                                    {{ $asset->condition }}
                                </span>
                            </li>
                            <li><strong>Location:</strong> {{ $asset->location }}</li>
                            <li><strong>Building:</strong> {{ $asset->building_name }}</li>
                            <li><strong>Fund:</strong> {{ $asset->fund }}</li>
                            <li><strong>Price (RM):</strong> {{ $asset->price !== null ? number_format($asset->price, 2) : '-' }}</li>
                            <li><strong>Year Acquired:</strong> {{ $asset->acquired_at ?? '-' }}</li>
                            <li><strong>Custodian:</strong> {{ $asset->custodian }}</li>
                        </ul>

                        @if($asset->latitude && $asset->longitude)
                        <div class="mt-4">
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $asset->latitude }},{{ $asset->longitude }}"
                               target="_blank" class="text-indigo-600 hover:text-indigo-800 dark:hover:text-indigo-400 text-sm inline-flex items-center gap-1">
                                <i class="fas fa-location-arrow"></i> Get Directions
                            </a>
                        </div>
                        @endif
                    </div>

                    <div class="p-4 flex flex-col justify-between items-center border-t dark:border-gray-600 md:border-t-0 md:border-l">
                        <div id="qr-container-{{ $asset->id }}"
                             class="mb-3 w-28 h-28 border p-2 bg-white dark:bg-gray-100 flex items-center justify-center overflow-hidden relative group">
                            <button onclick="loadQr({{ $asset->id }})"
                                    class="absolute z-10 bg-white/90 dark:bg-gray-800/90 text-sm px-2 py-1 rounded text-blue-600 font-medium group-hover:opacity-0 transition duration-300 ease-in-out">
                                Reveal QR
                            </button>
                        </div>

                        <div class="flex flex-col space-y-1 items-center text-sm">
                            <a href="{{ route('assets.qr.download', $asset->id) }}" class="text-gray-700 dark:text-gray-300 hover:text-black">
                                <i class="fas fa-download"></i> Download QR
                            </a>
                            <a href="{{ route('assets.edit', $asset) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('Delete this asset?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 dark:text-gray-300">No assets found.</p>
            @endforelse

            <div class="mt-6 px-4 flex justify-center md:col-span-2">
                {{ $assets->onEachSide(1)->links('vendor.pagination.simple-gap') }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function loadQr(id) {
        const container = document.getElementById(`qr-container-${id}`);
        if (!container) return;

        const img = document.createElement('img');
        img.src = `/assets/${id}/qr`;
        img.alt = 'QR Code';
        img.className = 'w-28 h-28 blur-sm transition duration-700 ease-in-out';

        container.innerHTML = '';
        container.appendChild(img);

        setTimeout(() => {
            img.classList.remove('blur-sm');
        }, 50);
    }
</script>
@endpush
