<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OwnerController extends Controller
{
    public function dashboard(): View|RedirectResponse
    {
        $user = Auth::user();
        
        // Check if owner profile exists
        if (!$user->owner) {
            return redirect()->route('dashboard'); // Redirect to common dashboard
        }
        
        $owner = $user->owner()->with('pets')->first();

        return view('owner.dashboard', [
            'pets' => $owner->pets
        ]);
    }

    public function pets(): View|RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user->owner) {
            return redirect()->route('dashboard');
        }
        
        $owner = $user->owner()->with('pets')->first();
        return view('owner.pets.index', ['pets' => $owner->pets]);
    }
}