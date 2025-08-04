<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Conversation extends Model
{
    use SoftDeletes;

    protected $fillable = ['user1_id', 'user2_id', 'deleted_by'];

    protected $casts = [
        'deleted_by' => 'array',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    // Get the other user in conversation
    public function getOtherUser(User $user)
    {
        return $user->id === $this->user1_id ? $this->user2 : $this->user1;
    }

    public function getDeletedByAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
        return is_array($value) ? $value : json_decode($value, true);
    }
}
