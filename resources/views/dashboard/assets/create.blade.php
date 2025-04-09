@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-3xl font-semibold mb-6 text-center">Add New Asset</h1>

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

    <!-- Create Asset Form -->
    <form action="{{ route('assets.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="name" class="block">Asset Name</label>
            <input type="text" name="name" id="name" class="form-input mt-1 block w-full" required>
        </div>

        <div class="mb-4">
            <label for="code" class="block">Asset Code</label>
            <input type="text" name="code" id="code" class="form-input mt-1 block w-full" required>
        </div>

        <div class="mb-4">
            <label for="type" class="block">Asset Type</label>
            <input type="text" name="type" id="type" class="form-input mt-1 block w-full" required>
        </div>

        <div class="mb-4">
            <label for="acquired_at" class="block">Acquired Date</label>
            <input type="date" name="acquired_at" id="acquired_at" class="form-input mt-1 block w-full" required>
        </div>

        <div class="mb-4">
            <label for="condition" class="block">Condition</label>
            <input type="text" name="condition" id="condition" class="form-input mt-1 block w-full" required>
        </div>

        <div class="mb-4">
            <label for="location" class="block">Location</label>
            <input type="text" name="location" id="location" class="form-input mt-1 block w-full" required>
        </div>

        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Save Asset</button>
    </form>
</div>
@endsection
