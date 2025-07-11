<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medication extends Model
{
    protected $fillable = [
        'medical_record_id',
        'name',
        'dosage',
        'frequency',
        'start_date',
        'end_date',
        'purpose'
    ];

    protected $casts = [
    'start_date' => 'date',
    'end_date' => 'date'
    ];

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
