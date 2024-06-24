<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BroadcastReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'broadcast_id', 'messages_sent', 'messages_delivered', 'messages_read', 'messages_responded'
    ];

    public function broadcast()
    {
        return $this->belongsTo(App\Moddels\Broadcast::class);
    }
}
