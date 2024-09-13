<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'phone', 'status', 'broadcast', 'sms'
    ];

    protected $with = [
        'attributes'
    ];

    public function attributes()
    {
        return $this->hasMany(ContactAttribute::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Registering a deleting event hook
        static::deleting(function ($contact) {
            // Delete the associated attributes
            $contact->attributes()->delete();
        });
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

}
