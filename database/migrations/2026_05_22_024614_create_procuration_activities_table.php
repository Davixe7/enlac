<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procuration_activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->boolean('is_active')->default(true);

            // Campos comunes de configuración avanzada
            $table->date('created_date')->nullable();
            $table->date('event_date')->nullable();

            // Campos específicos para Radiomaratón
            $table->decimal('goal_amount', 12, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procuration_activities');
    }
};
