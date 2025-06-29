@extends('layouts.app')

@section('title', 'Login')

@section('content')
<style>
    body {
        background-image: url('https://perlis.uitm.edu.my/images/2020/10/01/hep.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    }

    .login-card {
        background-color: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(8px);
    }

    .dark .login-card {
        background-color: rgba(17, 24, 39, 0.6);
    }
</style>

<div class="flex items-center justify-center min-h-screen px-4">
    <div class="w-full max-w-md login-card p-6 rounded-xl shadow-lg text-gray-900 dark:text-white">
        <h2 class="text-2xl font-semibold text-center mb-6">Login</h2>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-700 dark:bg-green-700 dark:text-white px-4 py-2 rounded mb-3 text-center">
                {{ session('success') }}
            </div>
        @endif

        @if(session('info'))
            <div class="bg-blue-100 text-blue-700 dark:bg-blue-700 dark:text-white px-4 py-2 rounded mb-3 text-center">
                {{ session('info') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 text-red-700 dark:bg-red-700 dark:text-white px-4 py-2 rounded mb-3 text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="relative z-0 w-full mb-5 group">
                <input type="email" name="email" id="email" required autofocus
                    class="peer block w-full appearance-none border-0 border-b-2 border-gray-300 dark:border-gray-600 bg-transparent py-2.5 px-0 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:outline-none focus:ring-0"
                    placeholder=" " />
                <label for="email"
                    class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0]
                        peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100
                        peer-focus:scale-75 peer-focus:-translate-y-6">
                    Email Address
                </label>
            </div>

            {{-- Password --}}
            <div class="relative z-0 w-full mb-5 group">
                <input type="password" name="password" id="password" required
                    class="peer block w-full appearance-none border-0 border-b-2 border-gray-300 dark:border-gray-600 bg-transparent py-2.5 px-0 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:outline-none focus:ring-0"
                    placeholder=" " />
                <label for="password"
                    class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0]
                        peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100
                        peer-focus:scale-75 peer-focus:-translate-y-6">
                    Password
                </label>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition">
                Login
            </button>

            <div class="mt-3 text-center">
                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    Forgot your password?
                </a>
            </div>

            <p class="mt-4 text-sm text-center text-gray-600 dark:text-gray-300">
                Don’t have an account?
                <a href="{{ route('register') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Register</a>
            </p>

            <div class="mt-4 text-center">
                <a href="{{ url()->previous() }}" class="text-sm text-gray-600 dark:text-gray-300 hover:underline">
                    ← Back
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
