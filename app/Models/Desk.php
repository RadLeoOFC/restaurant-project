<?php

// app/Models/Desk.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desk extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'status',
        'coordinates_x',
        'coordinates_y',
    ];

    // Новый аксессор
    public function getTranslatedNameAttribute()
    {
        $number = preg_replace('/[^0-9]/', '', $this->name);
        return __('messages.desk_number') . ' №' . $number;
    }
}


