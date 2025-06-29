@extends('layouts.app')

@section('title', 'Add New Asset')

@section('content')
@php
    $previousUrl = url()->previous();
    $isSamePage = $previousUrl === url()->current();
    $backUrl = $isSamePage ? route('dashboard') : $previousUrl;
@endphp

<div x-data="{ open: true }" class="relative z-50">
    <div x-show="open" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="-translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="-translate-y-full opacity-0"
         class="fixed inset-0 overflow-y-auto bg-white/30 dark:bg-gray-900/30 backdrop-blur-md flex justify-center items-start p-4">

        <div class="w-full max-w-3xl bg-white dark:bg-gray-800 bg-opacity-80 rounded-xl shadow-xl border border-gray-300 dark:border-gray-700 p-6 mt-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Add New Asset</h2>
                <button @click="open = false" class="text-gray-500 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            @if($errors->any())
            <div class="mb-6 bg-red-50 dark:bg-red-900 border-l-4 border-red-500 p-4 rounded-lg">
                <h3 class="text-red-800 dark:text-red-100 font-medium">Please fix the following errors:</h3>
                <ul class="ml-6 list-disc text-red-700 dark:text-red-200 text-sm">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('assets.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    @foreach([
                        ['name' => 'brand', 'label' => 'Brand', 'type' => 'text', 'required' => true],
                        ['name' => 'model', 'label' => 'Model', 'type' => 'text', 'required' => true],
                        ['name' => 'type', 'label' => 'Type', 'type' => 'text', 'required' => true],
                        ['name' => 'location', 'label' => 'Location', 'type' => 'text', 'required' => true],
                        ['name' => 'building_name', 'label' => 'Building Name', 'type' => 'text'],
                        ['name' => 'fund', 'label' => 'Fund', 'type' => 'text'],
                        ['name' => 'price', 'label' => 'Price (RM)', 'type' => 'number', 'step' => '0.01'],
                        ['name' => 'previous_custodian', 'label' => 'Previous Custodian', 'type' => 'text'],
                        ['name' => 'custodian', 'label' => 'Current Custodian', 'type' => 'text']
                    ] as $field)
                    <div>
                        <label for="{{ $field['name'] }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ $field['label'] }} {{ $field['required'] ?? false ? '*' : '' }}
                        </label>
                        <input
                            type="{{ $field['type'] }}"
                            name="{{ $field['name'] }}"
                            id="{{ $field['name'] }}"
                            value="{{ old($field['name']) }}"
                            {{ isset($field['required']) && $field['required'] ? 'required' : '' }}
                            @if(isset($field['step'])) step="{{ $field['step'] }}" @endif
                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                        >
                    </div>
                    @endforeach

                    {{-- Condition --}}
                    <div>
                        <label for="condition" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Condition *</label>
                        <select name="condition" id="condition" required class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            <option value="Good" {{ old('condition') == 'Good' ? 'selected' : '' }}>Good</option>
                            <option value="Fair" {{ old('condition') == 'Fair' ? 'selected' : '' }}>Fair</option>
                            <option value="Poor" {{ old('condition') == 'Poor' ? 'selected' : '' }}>Poor</option>
                        </select>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">{{ old('notes') }}</textarea>
                    </div>

                    {{-- Coordinates --}}
                    <div class="space-y-2">
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="getLocation()" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                <i class="fas fa-map-marker-alt"></i> Use My Location
                            </button>
                            <button type="button" onclick="resetMarker()" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                                <i class="fas fa-undo"></i> Reset Marker
                            </button>
                        </div>

                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pinpoint Location</label>
                        <div id="map" style="height: 300px;" class="rounded border dark:border-gray-600"></div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="latitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Latitude</label>
                                <input type="text" id="latitude" name="latitude" value="{{ old('latitude') }}" readonly class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md bg-gray-100">
                            </div>
                            <div>
                                <label for="longitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Longitude</label>
                                <input type="text" id="longitude" name="longitude" value="{{ old('longitude') }}" readonly class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md bg-gray-100">
                            </div>
                        </div>
                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-300" id="addressDisplay"></div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-5 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('assets.index') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-500 rounded-md text-sm text-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </a>
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i> Save Asset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let map, marker, geocoder;

function initMap() {
    const defaultLatLng = { lat: 6.4636, lng: 100.2996 };
    const lat = parseFloat(document.getElementById('latitude').value) || defaultLatLng.lat;
    const lng = parseFloat(document.getElementById('longitude').value) || defaultLatLng.lng;
    const position = { lat, lng };

    map = new google.maps.Map(document.getElementById("map"), {
        center: position,
        zoom: 16,
    });

    geocoder = new google.maps.Geocoder();

    marker = new google.maps.Marker({
        position,
        map,
        draggable: true,
    });

    map.addListener("click", (e) => {
        marker.setPosition(e.latLng);
        updateLatLngFields(e.latLng);
        reverseGeocode(e.latLng);
    });

    marker.addListener("dragend", (e) => {
        updateLatLngFields(e.latLng);
        reverseGeocode(e.latLng);
    });
}

function updateLatLngFields(latlng) {
    const lat = typeof latlng.lat === 'function' ? latlng.lat() : latlng.lat;
    const lng = typeof latlng.lng === 'function' ? latlng.lng() : latlng.lng;

    document.getElementById("latitude").value = lat.toFixed(8);
    document.getElementById("longitude").value = lng.toFixed(8);
}


function resetMarker() {
    marker.setPosition(map.getCenter());
    updateLatLngFields(map.getCenter());
    reverseGeocode(map.getCenter());
}

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
            const coords = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            map.setCenter(coords);
            marker.setPosition(coords);
            updateLatLngFields(coords);
            reverseGeocode(coords);
        }, (error) => {
            alert("Geolocation failed: " + error.message);
        });
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

function reverseGeocode(latlng) {
    geocoder.geocode({ location: latlng }, (results, status) => {
        if (status === "OK" && results[0]) {
            document.getElementById("addressDisplay").innerText = results[0].formatted_address;
        } else {
            document.getElementById("addressDisplay").innerText = "Address not found.";
        }
    });
}
</script>
<script async src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap"></script>
@endsection
