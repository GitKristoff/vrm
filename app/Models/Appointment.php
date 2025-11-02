<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

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
        'appointment_date' => 'datetime',
    ];

    // Ensure the appointment_date is always returned in the app timezone
    public function getAppointmentDateAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        // stored value is UTC, convert to app timezone
        return Carbon::parse($value, 'UTC')->setTimezone(config('app.timezone'));
    }

    // When setting appointment_date, accept values in app timezone and store as UTC
    public function setAppointmentDateAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['appointment_date'] = null;
            return;
        }

        // If it's already a Carbon instance, assume it's in app timezone
        if ($value instanceof Carbon) {
            $dt = $value->copy();
        } else {
            // parse incoming value using app timezone (e.g. Asia/Manila)
            $dt = Carbon::parse($value, config('app.timezone'));
        }

        // convert to UTC for storage
        $dtUtc = $dt->copy()->setTimezone('UTC');
        $this->attributes['appointment_date'] = $dtUtc->format('Y-m-d H:i:s');
    }

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
