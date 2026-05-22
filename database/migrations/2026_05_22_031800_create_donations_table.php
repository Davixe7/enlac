<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();

            // Relaciones y Datos del Donante
            $table->unsignedBigInteger('donor_id');
            $table->unsignedBigInteger('procuration_activity_id'); // Enlace al catálogo que hicimos
            $table->string('activity_type'); // Copia del tipo para agilizar consultas en el front

            // Información Financiera
            $table->string('folio_number')->unique(); // Ej: P-26-00001
            $table->text('concept')->nullable();
            $table->date('payment_date');
            $table->string('payment_method'); // Efectivo, Transferencia, etc.
            $table->string('reference')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency')->default('MXN'); // MXN / DLLS
            $table->decimal('exchange_rate', 10, 4)->nullable();
            $table->decimal('equivalent_amount_mxn', 12, 2)->nullable();

            // Recibo Deducible
            $table->boolean('has_tax_receipt')->default(false);
            $table->string('tax_receipt_number')->nullable();

            // CAMPOS DINÁMICOS POR TIPO DE ACTIVIDAD
            // Alcancías
            $table->string('piggy_bank_location')->nullable(); // Ubicada en

            // Alianza o Fundación
            $table->string('project_name')->nullable(); // Proyecto

            // Boteo
            $table->string('boteo_area')->nullable();
            $table->string('boteo_can_number')->nullable(); // No. De Bote
            $table->decimal('boteo_ten_percent', 12, 2)->nullable(); // 10% Boteo

            // Programa de Verano o Natación
            $table->unsignedBigInteger('beneficiary_id')->nullable(); // Select
            $table->string('external_name')->nullable(); // Nombre del Externo
            $table->string('group_name')->nullable(); // Grupo

            // Organismo de Gobierno
            $table->string('government_institution_name')->nullable(); // Nombre de la institución

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
