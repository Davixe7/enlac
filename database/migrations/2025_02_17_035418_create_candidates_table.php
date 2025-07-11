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
            $table->text('diagnosis');
            $table->unsignedBigInteger('sheet');

            $table->string('info_channel');
            $table->boolean('admission_status')->default(null)->nullable();
            $table->string('admission_comment')->default(null)->nullable();
            $table->date('entry_date')->default(null)->nullable();
            $table->string('entry_status')->default('pendiente_ingresar');
            $table->foreignId('program_id')->nullable()->constrained()->onDelete('cascade');
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
