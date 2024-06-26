<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'business_id', 'template', 'channel', 'status', 'recipients', 'sent_date', 'is_scheduled', 'posting_time'
    ];

    protected $casts = [
        'template'=> 'json'
    ];
}
