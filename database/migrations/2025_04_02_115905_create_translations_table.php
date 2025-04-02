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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('language_id');
            $table->string('key'); // Например: "desk_name_1"
            $table->text('value'); // Перевод
            $table->timestamps();
    
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
            $table->unique(['language_id', 'key']); // Предотвращаем дубли
        });
    }    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
