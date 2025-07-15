<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Veterinarian;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


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
            // Determine the actual role
            $role = $request->has('is_admin') ? 'admin' : 'veterinarian';

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $role,
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

    public function edit(Veterinarian $veterinarian)
    {
        return view('admin.veterinarians.edit', compact('veterinarian'));
    }

    public function update(Request $request, Veterinarian $veterinarian)
    {
        // Add your validation and update logic here
        $validated = $request->validate([
            'license_number' => 'required|string',
            'specialization' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'is_admin' => 'sometimes|boolean'

        ]);

        $veterinarian->update($validated);

        // Update user role if needed
        if ($veterinarian->user) {
            $newRole = $request->has('is_admin') ? 'admin' : 'veterinarian';
            if ($veterinarian->user->role !== $newRole) {
                $veterinarian->user->update(['role' => $newRole]);
            }
        }

        return redirect()->route('admin.veterinarians.show', $veterinarian)
            ->with('success', 'Veterinarian updated successfully');
    }

    public function show(Veterinarian $veterinarian)
    {
        $veterinarian->load('user');
        return view('admin.veterinarians.show', compact('veterinarian'));
    }

    public function destroy(Veterinarian $veterinarian)
    {
        try {
            DB::transaction(function () use ($veterinarian) {
                // Get the user associated with this veterinarian
                $user = $veterinarian->user;

                // Delete the veterinarian record
                $veterinarian->delete();

                // Delete the associated user
                $user->delete();
            });

            return redirect()->route('admin.veterinarians.index')
                ->with('success', 'Veterinarian deleted successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete veterinarian: ' . $e->getMessage());
        }
    }
}
