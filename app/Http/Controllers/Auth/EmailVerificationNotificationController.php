<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            $user = $request->user();
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'veterinarian') {
                return redirect()->route('vet.dashboard');
            }
            return redirect()->route('owner.dashboard');
        }

        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            Log::error('Mail transport error sending verification', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Mail service offline');
        }

        return back()->with('status', 'verification-link-sent');
    }
}
