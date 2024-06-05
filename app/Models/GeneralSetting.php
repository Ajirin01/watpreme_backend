<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'smsEnabled',
        'timezone',
        'language',
        'displayLogo',
        'business_id'
    ];

    protected $with = ['business'];
    protected $hidden = ['business_id'];

    public function business(){
        return $this->belongsTo('App\Models\Business');
    }

}
