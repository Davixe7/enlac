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
        Schema::create('payment_promises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procuration_activity_id')->constrained('procuration_activities')->onDelete('cascade');
            $table->foreignId('donor_id')->constrained('donors')->onDelete('cascade');
            $table->string('payment_type');
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->foreignId('radiomarathon_key_id')->constrained('radiomarathon_keys');
            $table->boolean('anonymous')->default(false);
            $table->boolean('deductible_receipt')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_promises');
    }
};
