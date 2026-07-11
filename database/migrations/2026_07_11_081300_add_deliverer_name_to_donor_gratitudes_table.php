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
        Schema::table('donor_gratitudes', function (Blueprint $table) {
            $table->string('deliverer_name')
                ->nullable()
                ->after('recipient_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donor_gratitudes', function (Blueprint $table) {
            $table->dropColumn('deliverer_name');
        });
    }
};
