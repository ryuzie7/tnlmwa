@extends('layouts.app')

@section('content')
<div class="p-6 max-w-xl mx-auto">
    <h1 class="text-xl font-bold mb-4">Log Asset Usage</h1>
    <form action="{{ route('usage.store') }}" method="POST">
        @csrf
        <select name="asset_id" class="w-full p-2 border mb-3 rounded" required>
            <option value="">Select Asset</option>
            @foreach($assets as $asset)
                <option value="{{ $asset->id }}">{{ $asset->name }}</option>
            @endforeach
        </select>
        <input type="date" name="date" class="w-full p-2 border mb-3 rounded" required>
        <textarea name="notes" placeholder="Notes" class="w-full p-2 border mb-3 rounded"></textarea>
        <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Save</button>
    </form>
</div>
@endsection
