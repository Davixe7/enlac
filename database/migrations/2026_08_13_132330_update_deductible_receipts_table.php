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
        Schema::table('deductible_receipts', function (Blueprint $table) {
            $table->string('street')->nullable()->change();
            $table->string('external_number')->nullable()->change();
            $table->string('neighborhood')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('zip_code')->nullable()->change();
            $table->string('state')->nullable()->change();
            $table->string('country')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deductible_receipts', function (Blueprint $table) {
            $table->string('street')->nullable(false)->change();
            $table->string('external_number')->nullable(false)->change();
            $table->string('neighborhood')->nullable(false)->change();
            $table->string('city')->nullable(false)->change();
            $table->string('zip_code')->nullable(false)->change();
            $table->string('state')->nullable(false)->change();
            $table->string('country')->nullable(false)->change();
        });
    }
};
