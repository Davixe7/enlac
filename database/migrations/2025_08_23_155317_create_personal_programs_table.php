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
        Schema::create('personal_programs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('plan_id');
            $table->foreignId('plan_type_id');
            $table->foreignId('candidate_id');
            $table->string('name');
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_programs');
    }
};
