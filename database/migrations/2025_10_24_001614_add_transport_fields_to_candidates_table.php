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
            $table->boolean('requires_transport')->default(false)->after('sheet'); 
            $table->string('transport_address')->nullable()->after('requires_transport');
            $table->string('transport_location_link')->nullable()->after('transport_address');
            $table->string('curp')->nullable()->after('transport_location_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn(['requires_transport', 'transport_address', 'transport_location_link', 'curp']);
        });
    }
};
