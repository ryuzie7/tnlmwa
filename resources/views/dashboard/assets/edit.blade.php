@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-3xl font-semibold mb-6 text-center">Edit Asset</h1>

    <!-- Display Validation Errors -->
    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Edit Asset Form -->
    <form action="{{ route('assets.update', $asset) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block">Asset Name</label>
            <input type="text" name="name" id="name" class="form-input mt-1 block w-full" value="{{ old('name', $asset->name) }}" required>
        </div>

        <div class="mb-4">
            <label for="code" class="block">Asset Code</label>
            <input type="text" name="code" id="code" class="form-input mt-1 block w-full" value="{{ old('code', $asset->code) }}" required>
        </div>

        <div class="mb-4">
            <label for="type" class="block">Asset Type</label>
            <input type="text" name="type" id="type" class="form-input mt-1 block w-full" value="{{ old('type', $asset->type) }}" required>
        </div>

        <div class="mb-4">
            <label for="acquired_at" class="block">Acquired Date</label>
            <input type="date" name="acquired_at" id="acquired_at" class="form-input mt-1 block w-full" value="{{ old('acquired_at', $asset->acquired_at->format('Y-m-d')) }}" required>
        </div>

        <div class="mb-4">
            <label for="condition" class="block">Condition</label>
            <input type="text" name="condition" id="condition" class="form-input mt-1 block w-full" value="{{ old('condition', $asset->condition) }}" required>
        </div>

        <div class="mb-4">
            <label for="location" class="block">Location</label>
            <input type="text" name="location" id="location" class="form-input mt-1 block w-full" value="{{ old('location', $asset->location) }}" required>
        </div>

        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Update Asset</button>
    </form>
</div>
@endsection
