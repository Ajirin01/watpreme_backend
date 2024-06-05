<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    use HasFactory;

    protected $with = ['user', 'team'];

    protected $fillable = [
        'user_id',
        // 'emailPhone',
        // 'role',
        'online_status',
        'business_id',
        'team_id',
        'lastLoginIP',
        'lastLoginDate',
        'status'
    ];

    // public function user(){
    //     return $this->belongsTo('App\Models\User');
    // }

    public function business(){
        return $this->belongsTo('App\Models\Business');
    }

    public function team(){
        return $this->belongsTo('App\Models\Team');
    }

    public function user(){
        return $this->belongsTo('App\Models\User');
    }
}
