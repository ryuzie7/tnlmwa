<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (! $user || ! $user->hasVerifiedEmail()) {
            return back()->withErrors(['email' => 'This email is not verified.']);
        }

        // Validate reCAPTCHA
        if (! $this->validateRecaptcha($request)) {
            return back()->withErrors(['email' => 'Failed reCAPTCHA validation.']);
        }

        // Send password reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    protected function validateRecaptcha(Request $request): bool
    {
        // Optional: bypass reCAPTCHA in local environment
        if (app()->isLocal()) {
            return true;
        }

        $token = $request->input('g-recaptcha-response');
        $secret = env('RECAPTCHA_SECRET_KEY');

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secret,
            'response' => $token,
            'remoteip' => $request->ip(),
        ]);

        // Log full response for debugging
        logger()->debug('reCAPTCHA response', $response->json());

        return $response->json('success') === true;
    }
}
