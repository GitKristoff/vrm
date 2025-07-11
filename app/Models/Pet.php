<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
        'species',
        'breed',
        'age',
        'weight',
        'medical_history',
        'allergies',
        'profile_image'
    ];


    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
