<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Owner extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'address',
    ];

    // Add relationship to User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Keep your existing relationships
    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }
}
