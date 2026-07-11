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
        Schema::table('candidate_locations', function (Blueprint $table) {
            // Se añade el campo como string y nullable por si no todos los registros tienen localidad asignada de inmediato.
            // Se posiciona justo después de 'candidate_id' para mantener el orden lógico.
            $table->string('locality_name')
                ->nullable()
                ->after('candidate_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidate_locations', function (Blueprint $table) {
            $table->dropColumn('locality_name');
        });
    }
};
