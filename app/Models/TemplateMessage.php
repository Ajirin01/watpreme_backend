<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'status',
        'language',
        'components',
        'uploaded'
    ];

    protected $casts = [
        'components' => 'json',
        'language' => 'json'
    ];
}
