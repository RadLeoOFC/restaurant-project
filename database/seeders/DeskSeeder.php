<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Desk;
use Carbon\Carbon;

class DeskSeeder extends Seeder
{
    public function run(): void
    {
        Desk::create([
            'name' => 'Desk №1',
            'capacity' => 4,
            'status' => 'available',
            'coordinates_x' => 10,
            'coordinates_y' => 20,
        ]);

        Desk::create([
            'name' => 'Desk №2',
            'capacity' => 2,
            'status' => 'occupied',
            'coordinates_x' => 15,
            'coordinates_y' => 25,
        ]);

        Desk::create([
            'name' => 'Desk №3',
            'capacity' => 6,
            'status' => 'selected',
            'coordinates_x' => 20,
            'coordinates_y' => 30,
        ]);
    }
}


