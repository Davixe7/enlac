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
            $table->boolean('equinetherapy_permission_medical')->default(false);
            $table->boolean('equinetherapy_permission_legal_guardian')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn('equinetherapy_permission_medical');
            $table->dropColumn('equinetherapy_permission_legal_guardian');
        });
    }
};
