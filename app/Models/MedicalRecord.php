<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalRecord extends Model
{
    protected $fillable = [
        'pet_id',
        'veterinarian_id',
        'appointment_id',
        'record_date',
        'subjective_notes',
        'objective_notes',
        'assessment',
        'plan',
        'temperature',
        'heart_rate',
        'respiratory_rate',
        'weight',
        'vaccination_history',
    ];

    protected $casts = [
    'record_date' => 'date',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function veterinarian(): BelongsTo
    {
        return $this->belongsTo(Veterinarian::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function medications(): HasMany
    {
        return $this->hasMany(Medication::class);
    }
}
