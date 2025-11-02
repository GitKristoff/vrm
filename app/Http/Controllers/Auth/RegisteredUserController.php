<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Owner;
use App\Models\Veterinarian;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'regex:/^09\d{9}$/', 'max:11'],
            'street' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:255'],
            'municipality' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'region' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
        ]);

        // Create user (always as owner)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'owner', // Force owner role
        ]);

        // Create owner profile
        Owner::create([
            'user_id' => $user->id,
            'phone' => $request->phone,
            'street' => $request->street,
            'barangay' => $request->barangay,
            'municipality' => $request->municipality,
            'province' => $request->province,
            'region' => $request->region,
            'country' => $request->country,
        ]);

        // Fire Registered event (email may be sent). Wrap to catch mail transport failures.
        try {
            event(new Registered($user));
        } catch (\Throwable $e) {
            // Log and redirect to login with friendly offline message instead of stack trace
            Log::error('Mail transport error during registration', ['message' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Email service is currently unavailable; you can log in and continue.')->setStatusCode(503);
        }

        Auth::login($user);

        return redirect()->route('login'); // Redirect to login page
    }
}
