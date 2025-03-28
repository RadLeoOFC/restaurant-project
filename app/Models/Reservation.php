<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    // Allow mass assignment for these fields
    protected $fillable = [
        'desk_id',
        'customer_id',
        'reservation_date',
        'reservation_time',
        'status',
    ];

    // Define relationship: Reservation belongs to a Desk
    public function desk()
    {
        return $this->belongsTo(Desk::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
