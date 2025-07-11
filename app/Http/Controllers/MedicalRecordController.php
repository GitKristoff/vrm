<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'owner') {
            $pets = $user->owner->pets;
            $petIds = $pets->pluck('id');
            $records = MedicalRecord::whereIn('pet_id', $petIds)
                ->with(['pet', 'veterinarian.user'])
                ->orderBy('record_date', 'desc')
                ->paginate(10);
        } elseif ($user->role === 'veterinarian') {
            $records = MedicalRecord::where('veterinarian_id', $user->veterinarian->id)
                ->with(['pet', 'veterinarian.user'])
                ->orderBy('record_date', 'desc')
                ->paginate(10);
        } else {
            $records = MedicalRecord::with(['pet', 'veterinarian.user'])
                ->orderBy('record_date', 'desc')
                ->paginate(10);
        }

        return view('medical-records.index', compact('records'));
    }

    public function show(Pet $pet)
    {
        $user = Auth::user();

        // Verify ownership for owners
        if ($user->role === 'owner' && $pet->owner_id !== $user->owner->id) {
            abort(403);
        }

        $records = MedicalRecord::where('pet_id', $pet->id)
            ->with(['veterinarian.user', 'medications'])
            ->orderBy('record_date', 'desc')
            ->paginate(10);

        return view('medical-records.show', compact('records', 'pet'));
    }
}
