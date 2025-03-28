<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    // Allow mass assignment
    protected $fillable = [
        'name',
        'email',
        'phone',
        'preferred_language',
    ];

    // A customer can have many reservations
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}

