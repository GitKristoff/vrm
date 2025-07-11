<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Veterinarian;
use Illuminate\Support\Facades\Hash;

class VeterinarianController extends Controller
{
    public function index()
    {
        $veterinarians = Veterinarian::with('user')->paginate(10);
        return view('admin.veterinarians.index', compact('veterinarians'));
    }

    public function create()
    {
        return view('admin.veterinarians.create');
    }

   public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'license_number' => 'required|string|unique:veterinarians,license_number',
            'specialization' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'password' => 'required|confirmed|min:8',
        ]);

        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'veterinarian',
            ]);

            // Create veterinarian profile
            Veterinarian::create([
                'user_id' => $user->id,
                'license_number' => $request->license_number,
                'specialization' => $request->specialization,
                'phone' => $request->phone,
                'is_admin' => $request->has('is_admin')
            ]);

            return redirect()->route('admin.veterinarians.index')
                ->with('success', 'Veterinarian created successfully');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create veterinarian: ' . $e->getMessage()]);
        }
    }
}
