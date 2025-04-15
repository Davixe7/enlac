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
        Schema::create('payment_configs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->foreignId('sponsor_id')->constrained()->onDelete('cascade');
            $table->decimal('amount');
            $table->unsignedTinyInteger('frequency');
            $table->unsignedTinyInteger('month_payday');
            $table->enum('address_type', ['home', 'office']);
            $table->boolean('wants_pickup')->default(false);
            $table->boolean('wants_reminder')->default(false);
            $table->boolean('wants_deductible_receipt')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_configs');
    }
};
