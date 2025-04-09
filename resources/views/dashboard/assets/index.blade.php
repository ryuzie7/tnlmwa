@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-3xl font-semibold mb-6 text-center">Asset Management</h1>

    @if(auth()->user()->role === 'admin')
    <div class="text-center mb-4">
        <a href="{{ route('assets.create') }}" class="btn btn-primary px-4 py-2 rounded shadow-md hover:bg-blue-700 transition">Add New Asset</a>
    </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-4">{{ session('error') }}</div>
    @endif

    <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
        <table class="table table-striped table-hover">
            <thead class="thead-dark bg-indigo-600 text-white">
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Acquired Date</th>
                    <th>Condition</th>
                    <th>Location</th>
                    <th>Custodian</th>
                    <th>Last Used</th>
                    <th>Purpose</th>
                    @if(auth()->user()->role === 'admin')
                    <th>Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $asset)
                <tr>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->code }}</td>
                    <td>{{ $asset->type }}</td>
                    <td>{{ $asset->acquired_at->format('Y-m-d') }}</td>
                    <td>{{ $asset->condition }}</td>
                    <td>{{ $asset->location }}</td>
                    <td>{{ optional($asset->custodian)->custodian_name ?? 'N/A' }}</td>
                    <td>{{ optional($asset->usageHistories->first())->used_at ?? 'N/A' }}</td>
                    <td>{{ optional($asset->usageHistories->first())->purpose ?? 'N/A' }}</td>
                    @if(auth()->user()->role === 'admin')
                    <td>
                        <a href="{{ route('assets.edit', $asset) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="inline-block">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Are you sure you want to delete this asset?')" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center p-4">No assets found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
