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
        Schema::create('raffles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procuration_activity_id')->constrained()->onDelete('cascade');
            $table->integer('tickets_count')->nullable();
            $table->decimal('ticket_price', 10, 2)->nullable();
            $table->string('place')->nullable();
            $table->string('winning_ticket')->nullable();
            $table->string('winner_name')->nullable();
            $table->string('seller_winner_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raffles');
    }
};
