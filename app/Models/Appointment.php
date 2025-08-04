<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
   protected $fillable = [
        'pet_id',
        'veterinarian_id',
        'appointment_date',
        'status',
        'reason',
        'notes',
        'duration_minutes',
        'type',
    ];

    protected $casts = [
        'appointment_date' => 'datetime'
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function veterinarian(): BelongsTo
    {
        return $this->belongsTo(Veterinarian::class);
    }

    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }
}
