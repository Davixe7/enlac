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
        //
        Schema::table('medical_records', function (Blueprint $table) {
            $table->integer('appointment_id')->nullable();
            $table->integer('type_id')->nullable();
            $table->integer('appointment_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn('appointment_id');
            $table->dropColumn('type_id');
            $table->dropColumn('appointment_type');
        });
    }
};
