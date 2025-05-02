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
        Schema::create('color_tiles', function (Blueprint $table) {
            $table->id(); // Optional: Primary key for the pivot table
            $table->foreignId('tile_id')->constrained()->onDelete('cascade');
            $table->foreignId('color_id')->constrained()->onDelete('cascade');
            $table->integer('priority')->nullable(); // Priority column for the pivot table
            $table->timestamps(); // created_at, updated_at
            $table->unique(['tile_id', 'color_id']); // Prevent duplicate tile-color pairs
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('color_tiles');
    }
};
