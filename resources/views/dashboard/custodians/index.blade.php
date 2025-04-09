@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Users</h1>

    @if(session('success'))
        <div class="bg-green-500 text-white p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500 text-white p-4 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <table class="w-full table-auto bg-white shadow rounded">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2">Name</th>
                <th class="p-2">Email</th>
                <th class="p-2">Phone</th>
                <th class="p-2">Staff ID</th>
                <th class="p-2">Approved</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($custodians as $custodian)
            <tr class="border-t">
                <td class="p-2">{{ $custodian->name }}</td>
                <td class="p-2">{{ $custodian->email }}</td>
                <td class="p-2">{{ $custodian->phone }}</td>
                <td class="p-2">{{ $custodian->staff_id }}</td>
                <td class="p-2">
                    @if($custodian->approved)
                        <span class="text-green-600">Yes</span>
                    @else
                        <span class="text-yellow-500">Pending</span>
                    @endif
                </td>
                {{-- <td class="p-2">
                    @if(!$custodian->approved)
                        <form action="{{ route('custodians.approve', $custodian) }}" method="POST" class="inline">
                            @csrf
                            <button class="text-blue-600">Approve</button>
                        </form>
                    @endif
                    <form action="{{ route('custodians.destroy', $custodian) }}" method="POST" class="inline ml-2">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Delete this user?')" class="text-red-600">Delete</button>
                    </form>
                </td> --}}
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
