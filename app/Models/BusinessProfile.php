<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    use HasFactory;

    protected $with = ['business'];

    protected $fillable = [
        'profilePicture',
        'phoneNumber',
        'about',
        'businessAddress',
        'businessDescription',
        'businessEmail',
        'businessIndustry',
        'website1',
        'website2',
        'business_id'
    ];

    public function business(){
        return $this->belongsTo('App\Models\Business');
    }
}
