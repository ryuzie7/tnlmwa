<x-mail::message>
# Password Changed

Hello {{ $user->name ?? 'User' }},

This is a confirmation that your password was changed successfully.

**Change Details:**
- **Time:** {{ \Carbon\Carbon::parse($timestamp)->format('l, d M Y h:i A') }}
- **IP Address:** {{ $ip }}

If you did not perform this action, please contact support immediately.

<x-mail::button :url="route('login')">
Login to Your Account
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
