<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('desks', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Desk name
            $table->integer('capacity'); // Number of people the desk can hold
            $table->enum('status', ['available', 'occupied', 'selected']); // Desk status
            $table->integer('coordinates_x'); // X coordinate
            $table->integer('coordinates_y'); // Y coordinate
            $table->timestamps(); // Created at & Updated at
        });        
    }

    public function down(): void
    {
        Schema::dropIfExists('desks');
    }
};

