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
        Schema::create('deductible_receipts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('payment_config_id')->onDelete('cascade');
            $table->string('rfc');
            $table->string('company_name');
            $table->string('fiscalRegime');
            $table->string('cfdi');
            $table->string('email');
            
            $table->string('street');
            $table->string('external_number');
            $table->string('neighborhood');
            $table->string('city');
            $table->string('zip_code');
            $table->string('state');
            $table->string('country');

            $table->string('observations')->nullable();
            $table->string('fiscalStatus')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deductible_receipts');
    }
};
