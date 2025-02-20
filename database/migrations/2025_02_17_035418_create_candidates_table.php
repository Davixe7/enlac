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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->date('birth_date');
            $table->integer('age');
            $table->integer('chronological_age');
            $table->text('diagnosis');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('evaluation_date');
            $table->string('photo')->nullable();
            $table->string('acceptance_status')->default(null)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
