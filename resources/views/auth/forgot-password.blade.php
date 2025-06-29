@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<style>
    body {
        background-image: url('https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEiFhIkpkNyI6sJ2B1znxjavoqbOg50JcgYku-7MJWVwsliJ_jivHedI36T3nHhAqRlAQnQ62PWAfY2_CNpPTz5aItno-IFIzEcHv7gm4d11fot_Lr6EXtrU-OKsWEEFY9YsNCjtRvkbUpQ/s1600/5.jpg');
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

<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md glass-card p-6 rounded-xl shadow-lg text-gray-900 dark:text-white">
        <h2 class="text-xl font-semibold text-center mb-4">Forgot Password</h2>

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-300">
            Forgot your password? No problem. Just enter your verified email address and we will send you a secure password reset link.
        </div>

        @if (session('status'))
            <div class="mb-4 text-green-600 dark:text-green-400 text-sm text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" id="forgot-password-form">
            @csrf

            <div class="relative z-0 w-full mb-5 group">
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    class="peer block w-full appearance-none border-0 border-b-2 border-gray-300 dark:border-gray-600 bg-transparent py-2.5 px-0 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:outline-none focus:ring-0"
                    placeholder=" "
                />
                <label
                    for="email"
                    class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0]
                        peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100
                        peer-focus:scale-75 peer-focus:-translate-y-6"
                >
                    Email
                </label>
                @if ($errors->has('email'))
                    <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $errors->first('email') }}</p>
                @endif
            </div>

            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

            <div class="flex items-center justify-between mt-6">
                <a href="{{ url()->previous() }}" class="text-sm text-gray-600 dark:text-gray-300 hover:underline">
                    ← Back
                </a>

                <button
                    type="submit"
                    onclick="submitWithRecaptcha(event)"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition"
                >
                    Email Password Reset Link
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>
<script>
    function submitWithRecaptcha(e) {
        e.preventDefault();
        grecaptcha.ready(function () {
            grecaptcha.execute('{{ env('RECAPTCHA_SITE_KEY') }}', {action: 'submit'}).then(function (token) {
                document.getElementById('g-recaptcha-response').value = token;
                document.getElementById('forgot-password-form').submit();
            });
        });
    }
</script>
@endsection
