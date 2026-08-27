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
        Schema::create('radiomarathon_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procuration_activity_id')->constrained('procuration_activities')->onDelete('cascade');
            $table->string('donor_name');
            $table->string('address');
            $table->string('donation_type');
            $table->string('amount');
            $table->string('collector');
            $table->boolean('has_tax_receipt')->default(false);
            $table->boolean('paid')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radiomarathon_calls');
    }
};
