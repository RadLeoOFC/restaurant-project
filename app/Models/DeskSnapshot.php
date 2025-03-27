<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeskSnapshot extends Model {
    protected $fillable = ['desk_id', 'coordinates_x', 'coordinates_y', 'snapshot_date'];

    public function desk() {
        return $this->belongsTo(Desk::class);
    }
}

