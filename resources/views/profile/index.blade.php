@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Profile</h1>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow text-gray-800 dark:text-white">
        <h3 class="text-lg font-semibold mb-4">Account Info</h3>

        <p class="mb-2"><strong>Name:</strong> {{ $user->name }}</p>
        <p class="mb-2"><strong>Email:</strong> {{ $user->email }}</p>
        <p class="mb-2"><strong>Role:</strong> {{ $user->role ?? 'User' }}</p>

        <div class="mt-6">
            <a href="{{ route('profile.edit') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Edit Profile
            </a>
        </div>
    </div>
</div>
@endsection
