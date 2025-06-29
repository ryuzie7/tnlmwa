<x-guest-layout>
    <div class="text-center text-sm text-gray-600 dark:text-gray-400">
        Thanks for signing up! Before getting started, please verify your email address by clicking the link we just emailed to you.
        If you didn’t receive the email, we will gladly send you another.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mt-3 text-green-600 dark:text-green-400 text-sm">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="mt-4 flex justify-center">
        @csrf
        <x-primary-button>Resend Verification Email</x-primary-button>
    </form>
</x-guest-layout>
