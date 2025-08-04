<?php

namespace App\Services;

use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class MedicalRecordService
{
    public function createRecord(array $data, $petId = null, $appointmentId = null)
    {
        $recordData = array_merge($data, [
            'pet_id' => $petId,
            'appointment_id' => $appointmentId,
        ]);

        $medicalRecord = MedicalRecord::create($recordData);

        // Generate PDF
        $pdf = Pdf::loadView('medical-records.report', compact('medicalRecord'));
        Storage::put('reports/medical-record-'.$medicalRecord->id.'.pdf', $pdf->output());

        // Add medications
        if (!empty($data['medications'])) {
            foreach ($data['medications'] as $medication) {
                $medicalRecord->medications()->create([
                    'name' => $medication['name'],
                    'dosage' => $medication['dosage'],
                    'frequency' => $medication['frequency'],
                    'start_date' => $medication['start_date'] ?? now(),
                    'end_date' => $medication['end_date'] ?? now()->addDays(7),
                    'purpose' => $medication['purpose'] ?? '',
                ]);
            }
        }

        return $medicalRecord;
    }
}
