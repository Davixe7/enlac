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
        Schema::create('brain_function_ranks', function (Blueprint $table) {
            $table->id();
            $table->enum('caracteristic', ['0', 'F', 'P']);
            $table->text('comments')->nullable();
            $table->enum('laterality_impact', ['l', 'r']);
            $table->foreignId('brain_level_id')->constrained();
            $table->foreignId('brain_function_id')->constrained();
            $table->foreignId('candidate_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brain_function_ranks');
    }
};
