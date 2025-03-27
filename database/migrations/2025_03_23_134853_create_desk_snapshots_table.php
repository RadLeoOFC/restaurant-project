<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('desk_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('desk_id');
            $table->integer('coordinates_x');
            $table->integer('coordinates_y');
            $table->date('snapshot_date');
            $table->timestamps();
        
            $table->foreign('desk_id')->references('id')->on('desks')->onDelete('cascade');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desk_snapshots');
    }
};
