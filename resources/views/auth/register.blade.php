@extends('layouts.app')

@section('title', 'Register')

@section('content')
<style>
    body {
        background-image: url('https://saltconference.wordpress.com/wp-content/uploads/2012/02/starcomplexii_1.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    }

    .glass-card {
        background-color: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(8px);
    }

    .dark .glass-card {
        background-color: rgba(17, 24, 39, 0.5);
    }
</style>

<div class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md glass-card p-6 rounded-xl shadow-lg text-gray-900 dark:text-white">
        <h2 class="text-2xl font-semibold text-center mb-6">Register</h2>

        @if ($errors->any())
        <div class="bg-red-100 text-red-700 dark:bg-red-700 dark:text-white px-4 py-2 rounded mb-4">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            @foreach ([
                ['id' => 'name', 'label' => 'Full Name', 'type' => 'text'],
                ['id' => 'staff_id', 'label' => 'Staff ID', 'type' => 'text'],
                ['id' => 'phone', 'label' => 'Phone Number', 'type' => 'text'],
                ['id' => 'email', 'label' => 'Email Address', 'type' => 'email']
            ] as $input)
                <div class="relative z-0 w-full mb-5 group">
                    <input
                        id="{{ $input['id'] }}"
                        name="{{ $input['id'] }}"
                        type="{{ $input['type'] }}"
                        value="{{ old($input['id']) }}"
                        required
                        class="peer block w-full appearance-none border-0 border-b-2 border-gray-300 dark:border-gray-600 bg-transparent py-2.5 px-0 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:outline-none focus:ring-0"
                        placeholder=" "
                    />
                    <label
                        for="{{ $input['id'] }}"
                        class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0]
                            peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100
                            peer-focus:scale-75 peer-focus:-translate-y-6"
                    >
                        {{ $input['label'] }}
                    </label>
                </div>
            @endforeach

            {{-- Password --}}
            <div class="relative z-0 w-full mb-5 group">
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    oninput="checkStrength(this.value)"
                    class="peer block w-full appearance-none border-0 border-b-2 border-gray-300 dark:border-gray-600 bg-transparent py-2.5 px-0 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:outline-none focus:ring-0"
                    placeholder=" "
                />
                <label
                    for="password"
                    class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0]
                        peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100
                        peer-focus:scale-75 peer-focus:-translate-y-6"
                >
                    Password
                </label>
                <p id="strengthResult" class="text-xs mt-1 text-gray-500 dark:text-gray-300">Strength: -</p>
            </div>

            {{-- Confirm Password --}}
            <div class="relative z-0 w-full mb-6 group">
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    class="peer block w-full appearance-none border-0 border-b-2 border-gray-300 dark:border-gray-600 bg-transparent py-2.5 px-0 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:outline-none focus:ring-0"
                    placeholder=" "
                />
                <label
                    for="password_confirmation"
                    class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0]
                        peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100
                        peer-focus:scale-75 peer-focus:-translate-y-6"
                >
                    Confirm Password
                </label>
            </div>

            <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded transition">
                Register
            </button>

            <p class="mt-4 text-sm text-center text-gray-600 dark:text-gray-300">
                Already have an account?
                <a href="{{ route('login') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Login</a>
            </p>

            <div class="mt-4 text-center">
                <a href="{{ url()->previous() }}" class="text-sm text-gray-600 dark:text-gray-300 hover:underline">
                    ← Back
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Password strength checker --}}
<script>
    function checkStrength(password) {
        let strength = 'Weak';
        if (password.length > 8 && /[A-Z]/.test(password) && /\d/.test(password)) {
            strength = 'Strong';
        } else if (password.length >= 6) {
            strength = 'Moderate';
        }
        document.getElementById('strengthResult').innerText = 'Strength: ' + strength;
    }
</script>
@endsection
