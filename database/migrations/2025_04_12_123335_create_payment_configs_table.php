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
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->foreignId('sponsor_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('sponsorship_id')->constrained()->onDelete('cascade');

            $table->decimal('amount', 10, 2);
            $table->decimal('frequency', 4, 2);
            $table->date('effective_since');
            $table->date('effective_until')->nullable();
            $table->timestamps();

            $table->index(['sponsorship_id', 'effective_since', 'effective_until'], 'payment_cfg_snapshots_period_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_config_snapshots');
    }
};

