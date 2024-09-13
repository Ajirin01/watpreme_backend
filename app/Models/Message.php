<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['conversation_id', 'sender', 'receiver', 'message', 'status'];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    protected $casts = [
        'sender' => 'array',
        'receiver' => 'array'
    ];
}
