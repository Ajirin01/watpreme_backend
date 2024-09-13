<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone_number_id',
    ];

    protected $with = ['businessAdminUsers'];

    public function generalSetting(){
        return $this->hasOne('App\Models\GeneralSetting');
    }

    public function operators(){
        return $this->hasMany('App\Models\Operator');
    }

    public function business_profile(){
        return $this->hasOne('App\Models\BusinessProfile');
    }

    // public function users(){
    //     return $this->hasMany('App\Models\User');
    // }

    
    public function businessAdminUsers()
    {
        return $this->hasMany('App\Models\User')->where('role', 'business_admin');
    }

    public function getAdminUserById($adminUserId)
    {
        return $this->businessAdminUsers()->where('id', $adminUserId)->first();
    }
}
