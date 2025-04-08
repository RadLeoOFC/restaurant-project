<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportTemplate extends Model
{
    protected $fillable = [
        'name',
        'filters',
    ];

    // Automatically cast filters JSON to array
    protected $casts = [
        'filters' => 'array',
    ];
}

