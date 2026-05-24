<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capacitations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->string('start_time'); // Guardará el string AM/PM enviado por Quasar
            $table->string('end_time');   // Guardará el string AM/PM enviado por Quasar
            $table->string('location');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capacitations');
    }
};
