<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cambia la columna frequency de unsignedTinyInteger a decimal(4,2).
 * Valores esperados: mínimo 0.5 (quincenal), máximo 12.
 * Requiere: composer require doctrine/dbal (para ->change()).
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_configs', function (Blueprint $table) {
            $table->decimal('frequency', 4, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_configs', function (Blueprint $table) {
            $table->unsignedTinyInteger('frequency')->change();
        });
    }
};
