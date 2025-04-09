@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Usage History</h1>
    <a href="{{ route('usagehistory.index') }}" class="mb-4 inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Usage History</a>

    <table class="w-full table-auto bg-white shadow rounded">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2">Asset</th>
                <th class="p-2">Custodian</th>
                <th class="p-2">Date</th>
                <th class="p-2">Notes</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usages as $usage)
            <tr class="border-t">
                <td class="p-2">{{ $usage->asset->name }}</td>
                <td class="p-2">{{ $usage->user->name }}</td>
                <td class="p-2">{{ $usage->date }}</td>
                <td class="p-2">{{ $usage->notes }}</td>
                <td class="p-2">
                    <form action="{{ route('usage.destroy', $usage) }}" method="POST">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Delete this record?')" class="text-red-600">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
