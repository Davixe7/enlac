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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->string('relationship');
            $table->boolean('enlac_responsible');
            $table->boolean('legal_guardian');
            $table->string('email');
            $table->string('whatsapp');
            $table->string('home_phone');

            $table->string('street');
            $table->string('neighborhood');
            $table->string('state');
            $table->string('postal_code');
            $table->string('exterior_number');
            $table->string('city');
            $table->string('country');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
