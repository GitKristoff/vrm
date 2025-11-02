<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
    */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->redirectByRole($user);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->redirectByRole($user);
    }

    protected function redirectByRole($user): RedirectResponse
    {
        return match ($user->role ?? 'owner') {
            'admin' => redirect()->route('admin.dashboard')->with('verified', 1),
            'veterinarian' => redirect()->route('vet.dashboard')->with('verified', 1),
            'owner' => redirect()->route('owner.dashboard')->with('verified', 1),
            default => redirect('/')->with('verified', 1),
        };
    }
}
