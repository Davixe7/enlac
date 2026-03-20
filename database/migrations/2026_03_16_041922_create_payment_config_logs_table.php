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
        Schema::create('payment_config_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_config_id')->constrained('payment_configs')->onDelete('cascade');
            $table->string('action'); // 'cancelado' o 'restaurado'
            $table->text('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_config_logs');
    }
};
