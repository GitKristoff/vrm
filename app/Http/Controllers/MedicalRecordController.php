<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Veterinarian;
use Illuminate\Support\Facades\Storage;
use App\Services\MedicalRecordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function create(Pet $pet)
    {
        $veterinarians = Veterinarian::with('user')->get();
        return view('medical-records.create', compact('pet', 'veterinarians'));
    }

    public function store(Request $request, Pet $pet)
    {
        $request->validate([
            'veterinarian_id' => 'required|exists:veterinarians,id',
            'record_date' => 'required|date',
            'subjective_notes' => 'required|string',
            'objective_notes' => 'required|string',
            'assessment' => 'required|string',
            'plan' => 'required|string',
            'temperature' => 'nullable|numeric',
            'heart_rate' => 'nullable|numeric',
            'respiratory_rate' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'medications' => 'nullable|array',
            'medications.*.name' => 'required_with:medications|string',
            'medications.*.dosage' => 'required_with:medications|string',
            'medications.*.frequency' => 'required_with:medications|string',
        ]);

        $service = new MedicalRecordService();

            $service->createRecord([
                'veterinarian_id' => $request->veterinarian_id,
                'record_date' => $request->record_date,
                'subjective_notes' => $request->subjective_notes,
                'objective_notes' => $request->objective_notes,
                'assessment' => $request->assessment,
                'plan' => $request->plan,
                'temperature' => $request->temperature,
                'heart_rate' => $request->heart_rate,
                'respiratory_rate' => $request->respiratory_rate,
                'weight' => $request->weight,
                'medications' => $request->medications ?? []
            ], $pet->id);

        return redirect()->route('pets.show', $pet)
            ->with('success', 'Medical record created successfully!');
    }

    public function show(Pet $pet)
    {
        $records = $pet->medicalRecords()->with('appointment')->orderBy('record_date', 'desc')->paginate(10);

        // Group appointments by type
        $types = ['vaccination', 'dental', 'checkup', 'surgery', 'other'];
        $typeCounts = [];
        foreach ($types as $type) {
            $typeCounts[$type] = $pet->appointments()->where('type', $type)->count();
        }

        return view('medical-records.show', compact('pet', 'records', 'typeCounts'));
    }

    public function archive(MedicalRecord $record)
    {
        $record->update(['is_archived' => true]);
        return back()->with('success', 'Medical record archived!');
    }

    public function archived()
    {
        $records = MedicalRecord::onlyArchived()
            ->with(['pet', 'veterinarian.user'])
            ->paginate(10);
        return view('medical-records.archived', compact('records'));
    }

    public function restore($id)
    {
        $record = MedicalRecord::findOrFail($id);
        $record->update(['is_archived' => false]);
        return back()->with('success', 'Medical record restored!');
    }

    public function downloadPdf(Pet $pet)
    {
        $medicalRecords = $pet->medicalRecords()->with('appointment', 'veterinarian.user')->orderBy('record_date', 'desc')->get();

        $pdf = Pdf::loadView('medical-records.pdf', compact('pet', 'medicalRecords'));
        return $pdf->download($pet->name . '_medical_records.pdf');
    }
}
