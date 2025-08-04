<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestResult extends Model
{
    protected $fillable = [
        'medical_record_id',
        'test_name',
        'result',
        'test_date',
        'lab_name',
        'file_path'
    ];

    protected $casts = [
        'test_date' => 'date'
    ];

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
