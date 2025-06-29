@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Edit Profile</h1>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-200 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow text-gray-800 dark:text-white">
        <form action="{{ route('profile.update') }}" method="POST" id="editProfileForm">
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                <input type="text" name="name" id="name" required autofocus
                    value="{{ old('name', $user->name) }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2">
                <p class="text-sm text-red-500 hidden" id="nameError">Name is required.</p>
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email (read-only)</label>
                <input type="email" id="email" disabled
                    value="{{ $user->email }}"
                    class="mt-1 block w-full bg-gray-100 dark:bg-gray-700 dark:text-white border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2">
            </div>

            {{-- Password --}}
            <div class="mb-4 relative">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password (optional)</label>
                <input type="password" name="password" id="password"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2 pr-10">
                <button type="button" onclick="toggleVisibility('password')" class="absolute right-3 top-10 text-gray-500">
                    <i class="fas fa-eye" id="toggleIcon_password"></i>
                </button>
            </div>

            {{-- Confirm Password --}}
            <div class="mb-6 relative">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2 pr-10">
                <button type="button" onclick="toggleVisibility('password_confirmation')" class="absolute right-3 top-10 text-gray-500">
                    <i class="fas fa-eye" id="toggleIcon_password_confirmation"></i>
                </button>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-between">
                <a href="{{ route('profile.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-500 text-gray-700 dark:text-white rounded hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center px-6 py-2 border border-transparent text-white bg-blue-600 rounded hover:bg-blue-700">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Password Toggle + Validation Script --}}
@push('scripts')
<script>
    function toggleVisibility(id) {
        const input = document.getElementById(id);
        const icon = document.getElementById('toggleIcon_' + id);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Simple client-side validation for name
    document.getElementById('editProfileForm').addEventListener('submit', function(e) {
        const nameField = document.getElementById('name');
        const nameError = document.getElementById('nameError');
        if (!nameField.value.trim()) {
            e.preventDefault();
            nameError.classList.remove('hidden');
        } else {
            nameError.classList.add('hidden');
        }
    });
</script>
@endpush
@endsection
