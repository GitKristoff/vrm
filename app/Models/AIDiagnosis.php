<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIDiagnosis extends Model
{
    protected $fillable = [
        'medical_record_id',
        'symptoms',
        'possible_conditions',
        'recommended_treatments',
        'medication_interactions',
        'confidence_score',
        'ai_model_version',
        'explanation'
    ];

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
