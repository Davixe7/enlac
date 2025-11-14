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
        Schema::table('candidates', function (Blueprint $table) {
            $table->boolean('requires_equinotherapy')->default(false)->after('requires_transport');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn('requires_equinotherapy');
        });

    }
};
