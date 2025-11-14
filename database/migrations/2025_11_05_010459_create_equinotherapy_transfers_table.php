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
        Schema::create('equinotherapy_transfers', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->time('ida')->nullable();
            $table->time('regreso')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equinotherapy_transfers');
    }
};
