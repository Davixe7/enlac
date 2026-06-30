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
        Schema::create('raffle_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raffle_id')->constrained()->onDelete('cascade');
            $table->foreignId('donor_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('raffle_seller_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('number');
            $table->boolean('cow')->default(false);
            $table->boolean('deductible_receipt')->default(false);
            $table->boolean('enlac_collection')->default(false);
            $table->string('status');
            $table->date('sold_at')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raffle_tickets');
    }
};
