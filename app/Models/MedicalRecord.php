<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalRecord extends Model
{
     use SoftDeletes;

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
        'is_archived',
    ];

    protected $casts = [
    'record_date' => 'date',
    ];

    public function scopeOnlyArchived($query)
    {
        return $query->where('is_archived', true);
    }
    public function scopeExcludeArchived($query)
    {
        return $query->where('is_archived', false);
    }
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
