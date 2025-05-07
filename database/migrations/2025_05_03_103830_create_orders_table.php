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
            // $table->string('product_image')->nullable(); // From "2. Name" section
            $table->longText('image_svg_text')->nullable(); // From "2. Name" section
            $table->string('name'); // From "2. Name" section
            $table->string('email'); // From email field
            $table->string('phone_number'); // From Phone no field
            $table->integer('quantity')->nullable(); // From Quantity r (possibly "required"?)
            $table->string('quantity_per_unit')->nullable(); // From Quantity per unit
            $table->string('status')->default('pending'); // From Status section
            $table->string('refer_by')->default('refer_by'); // From Status section
            $table->text('notes')->nullable(); // For "Create to message" and "Message" sections
            $table->text('message')->nullable();
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
