@extends('layouts.app')

@section('title', 'Asset QR Code')

@section('content')
<div class="w-full max-w-xl mx-auto px-4 py-10">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 space-y-6">

        <h2 class="text-xl sm:text-2xl font-bold text-center text-gray-800 dark:text-white">
            QR Code for Asset: {{ $asset->property_number }}
        </h2>

        <div class="flex justify-center">
            <div class="bg-white p-4 rounded border dark:border-gray-600">
                {!! $qrCode !!}
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
            <a href="{{ route('assets.qr.download', $asset) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded shadow transition">
                <i class="fas fa-download mr-2"></i> Download QR Code
            </a>

            <a href="{{ route('assets.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded shadow transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Assets
            </a>
        </div>

    </div>
</div>
@endsection
