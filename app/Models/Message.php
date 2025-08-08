<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['conversation_id', 'user_id', 'body', 'attachment', 'read_at'];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFileTypeAttribute()
    {
        if (!$this->attachment) return null;

        $extension = pathinfo($this->attachment, PATHINFO_EXTENSION);
        $extension = strtolower($extension);

        $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $videoTypes = ['mp4', 'mov', 'avi'];
        $audioTypes = ['mp3', 'wav'];

        if (in_array($extension, $imageTypes)) return 'image';
        if (in_array($extension, $videoTypes)) return 'video';
        if (in_array($extension, $audioTypes)) return 'audio';

        return 'file';
    }
}
