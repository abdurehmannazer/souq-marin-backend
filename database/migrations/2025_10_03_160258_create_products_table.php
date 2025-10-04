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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // relationship with users


            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('price' )->nullable();
            $table->string('city')->nullable();
            $table->json('categories')->nullable(); // array of categories
            $table->json('files')->nullable();      // array of file paths/names


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
