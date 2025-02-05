<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'message',
        'street', 'state', 'zip', 'country',
        'images', 'files'
    ];
    
    protected $casts = [
        'images' => 'array',
        'files' => 'array'
    ];
} 