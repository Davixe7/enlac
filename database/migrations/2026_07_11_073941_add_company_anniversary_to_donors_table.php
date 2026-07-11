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
        Schema::table('donors', function (Blueprint $blueprint) {
            // Se usa string para guardar el formato 'DD/MM' (ej: '05/11')
            $blueprint->string('company_anniversary', 5)
                ->nullable()
                ->after('company_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donors', function (Blueprint $blueprint) {
            $blueprint->dropColumn('company_anniversary');
        });
    }
};
