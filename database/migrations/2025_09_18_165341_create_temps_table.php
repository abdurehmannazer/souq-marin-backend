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
        Schema::create('temps', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); // add UUID column
            $table->string("phone");
            $table->string("OTP")->nullable();
            $table->string("password")->nullable();
            $table->string("user_name")->nullable();

            $table->enum('tempState', ['register', 'login', 'passwordForget']);


            $table->boolean('is_verified')->default(false); // mark after verification




            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temps');
    }
};
