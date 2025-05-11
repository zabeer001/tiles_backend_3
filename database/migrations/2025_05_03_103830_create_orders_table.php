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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // From "2. Name" section
            $table->string('email'); // From email field
            $table->string('phone_number'); // From Phone no 
            $table->text('message')->nullable();
            $table->string('tile_name');
            $table->string('quantity_unit')->default('Units'); // From Quantity per unit
            $table->integer('quantity_needed');
            $table->string('status')->default('pending'); // From Status section
            $table->string('referred_by')->nullable();
            $table->string('other_specify')->nullable();
            $table->string('grout_color')->nullable();
            $table->string('grout_thickness')->nullable();
            $table->string('grid_category')->nullable();
            $table->json('rotations')->nullable();
            $table->longText('svg_base64')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
