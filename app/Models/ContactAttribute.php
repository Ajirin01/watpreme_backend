<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'value', 'custom_attribute_id', 'contact_id'
    ];

    protected $with = [
        'customAttribute'
    ];

    public function customAttribute()
    {
        return $this->belongsTo(CustomAttribute::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    // Hide the foreign keys from JSON output
    protected $hidden = ['custom_attribute_id', 'contact_id'];
}
