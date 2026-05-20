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
        Schema::create('radiomarathon_keys', function (Blueprint $table) {
            $table->id();
            $table->string('code');           // CLAVE (ej: 1.1)
            $table->string('classification'); // CATEGORÍA / Clasificación (ej: Día Evento)
            $table->string('concept');        // CONCEPTO (ej: Donativos pagados en efectivo)
            $table->boolean('is_active')->default(true); // ESTATUS (Activo / Inactivo)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radiomarathon_keys');
    }
};
