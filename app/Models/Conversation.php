<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'business_id', // Add this line
        'start_time',
        'end_time',
        'status',
        'uuid',
    ];

    protected $with = ['contact'];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class); // Add this method
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
