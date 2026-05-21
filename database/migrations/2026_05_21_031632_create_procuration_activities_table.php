<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fundraising_activities', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ejemplo: "Sorteo Anual 2026"
            $table->string('type'); // Ejemplo: "Obsequio entre Amigos", "Boteo"
            $table->boolean('is_active')->default(true);

            // Campos de configuración dinámica (opcionales)
            $table->decimal('goal_amount', 12, 2)->nullable();
            $table->decimal('ticket_price', 10, 2)->nullable();
            $table->integer('tickets_goal')->nullable();
            $table->date('event_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fundraising_activities');
    }
};
