<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SublimationLocation extends Model
{
    protected $fillable = [
        'name',
        'order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
