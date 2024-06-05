<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;
    
    protected $fillable =  [
        'name', 'default', 'size', 'business_id'
    ];

    public function operators(){
        return $this->hasMany('App\models\Operator');
    }

    public function business(){
        return $this->belongsTo('App\Models\Business');
    }
}
