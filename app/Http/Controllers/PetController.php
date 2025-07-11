<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Added for debugging

class PetController extends Controller
{
    public function index()
    {
        $owner = Auth::user()->owner;
        $pets = $owner->pets()->latest()->get();

        return view('owner.pets.index', compact('pets'));
    }

    public function create()
    {
        return view('pets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'species' => [
                'required',
                Rule::in(['Dog', 'Cat', 'Bird', 'Rabbit', 'Rodent', 'Reptile', 'Fish', 'Other'])
            ],
            'breed' => 'nullable|string|max:100',
            'age' => 'required|integer|min:0|max:50',
            'weight' => 'nullable|numeric|min:0|max:200',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        $owner = Auth::user()->owner;

        if (!$owner) {
            return redirect()->route('owner.dashboard')
                ->with('error', 'Owner profile not found!');
        }

        $data = $request->except('profile_image');

        // DEBUG: Log file upload info
        // if ($request->hasFile('profile_image')) {
        //     Log::debug('Profile image received', [
        //         'file_name' => $request->file('profile_image')->getClientOriginalName(),
        //         'file_size' => $request->file('profile_image')->getSize(),
        //         'file_type' => $request->file('profile_image')->getMimeType()
        //     ]);
        // }

        // Handle image upload with additional checks
        if ($request->hasFile('profile_image')) {
            try {
                $path = $request->file('profile_image')->store('pet-profiles', 'public');
                $data['profile_image'] = $path;

                // DEBUG: Verify storage
                Log::debug('Image stored', [
                    'path' => $path,
                    'full_path' => storage_path('app/public/' . $path),
                    'exists' => Storage::disk('public')->exists($path) ? 'YES' : 'NO'
                ]);
            } catch (\Exception $e) {
                Log::error('Image upload failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return back()->withInput()
                    ->with('error', 'Image upload failed: ' . $e->getMessage());
            }
        }

        $pet = new Pet($data);
        $pet->owner_id = $owner->id;
        $pet->save();

        // DEBUG: Log created pet
        Log::info('Pet created', [
            'pet_id' => $pet->id,
            'image_path' => $pet->profile_image,
            'image_url' => $pet->profile_image ? asset('storage/' . $pet->profile_image) : null
        ]);

        return redirect()->route('owner.pets.index')
            ->with('success', 'Pet registered successfully!');
    }

    public function show(Pet $pet)
    {
        // Verify ownership
        if (Auth::user()->role === 'owner' && $pet->owner_id !== Auth::user()->owner->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('pets.show', compact('pet'));
    }

    public function edit(Pet $pet)
    {
        // Verify ownership
        if (Auth::user()->role === 'owner' && $pet->owner_id !== Auth::user()->owner->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('pets.edit', compact('pet'));
    }

    public function update(Request $request, Pet $pet)
    {
        // Verify ownership
        if (Auth::user()->role === 'owner' && $pet->owner_id !== Auth::user()->owner->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'species' => [
                'required',
                Rule::in(['Dog', 'Cat', 'Bird', 'Rabbit', 'Rodent', 'Reptile', 'Fish', 'Other'])
            ],
            'breed' => 'nullable|string|max:100',
            'age' => 'required|integer|min:0|max:50',
            'weight' => 'nullable|numeric|min:0|max:200',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        // DEBUG: Log existing image
        if ($pet->profile_image) {
            Log::debug('Existing pet image', [
                'path' => $pet->profile_image,
                'exists' => Storage::disk('public')->exists($pet->profile_image) ? 'YES' : 'NO'
            ]);
        }

        // Handle image update with additional checks
        if ($request->hasFile('profile_image')) {
            try {
                // Delete old image if exists
                if ($pet->profile_image) {
                    $deleted = Storage::disk('public')->delete($pet->profile_image);
                    Log::debug('Old image deleted', [
                        'path' => $pet->profile_image,
                        'success' => $deleted ? 'YES' : 'NO'
                    ]);
                }

                $path = $request->file('profile_image')->store('pet-profiles', 'public');
                $data['profile_image'] = $path;

                // DEBUG: Verify new image
                Log::debug('New image stored', [
                    'path' => $path,
                    'exists' => Storage::disk('public')->exists($path) ? 'YES' : 'NO'
                ]);
            } catch (\Exception $e) {
                Log::error('Image update failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return back()->withInput()
                    ->with('error', 'Image upload failed: ' . $e->getMessage());
            }
        } else {
            unset($data['profile_image']);
        }

        $pet->update($data);

        // DEBUG: Log updated pet
        // Log::info('Pet updated', [
        //     'pet_id' => $pet->id,
        //     'image_path' => $pet->profile_image,
        //     'image_url' => $pet->profile_image ? asset('storage/' . $pet->profile_image) : null
        // ]);

        return redirect()->route('owner.pets.index')
            ->with('success', 'Pet updated successfully!');
    }

    public function destroy(Pet $pet)
    {
        // Verify ownership
        if (Auth::user()->role === 'owner' && $pet->owner_id !== Auth::user()->owner->id) {
            abort(403, 'Unauthorized action.');
        }

        // DEBUG: Log image before deletion
        // if ($pet->profile_image) {
        //     Log::debug('Deleting pet image', [
        //         'path' => $pet->profile_image,
        //         'exists' => Storage::disk('public')->exists($pet->profile_image) ? 'YES' : 'NO'
        //     ]);
        // }

        // Cancel appointments
        $pet->appointments()
            ->where('status', 'Scheduled')
            ->update(['status' => 'Cancelled']);

        // Delete image if exists
        if ($pet->profile_image) {
            $deleted = Storage::disk('public')->delete($pet->profile_image);
            Log::info('Image deleted', [
                'path' => $pet->profile_image,
                'success' => $deleted ? 'YES' : 'NO'
            ]);
        }

        $pet->delete();
        return redirect()->route('owner.pets.index')
            ->with('success', 'Pet and associated appointments have been removed!');
    }

}
