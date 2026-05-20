<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donor_fiscal_records', function (Blueprint $table) {
            $table->id();
            // Relación con el Donante principal
            $table->foreignId('donor_id')->constrained()->onDelete('cascade');

            // Datos Fiscales
            $table->string('commercial_name');
            $table->string('logo_path')->nullable();
            $table->string('tax_name'); // Razón / Denominación Social
            $table->string('rfc');
            $table->string('street');
            $table->string('exterior_number');
            $table->string('neighborhood');
            $table->string('postal_code');
            $table->string('city');
            $table->string('state');
            $table->string('email');
            $table->string('tax_regimen'); // Régimen Fiscal (Dropdown)
            $table->string('cfdi_use');    // Uso de CFDI (Dropdown)
            $table->string('tax_status_certificate_path')->nullable(); // Constancia de Situación Fiscal
            $table->date('company_anniversary')->nullable();

            // Datos de Cobranza
            $table->string('billing_contact_name');
            $table->string('billing_job_title')->nullable();
            $table->string('billing_landline')->nullable();
            $table->string('billing_cellphone')->nullable();
            $table->string('billing_email')->nullable();
            $table->date('billing_birth_date')->nullable();
            $table->boolean('home_collection')->default(false); // Cobro a Domicilio Sí / No
            $table->string('payment_day')->nullable();

            // Domicilio de Cobranza (Opcionales si son diferentes al fiscal)
            $table->string('billing_street')->nullable();
            $table->string('billing_exterior_number')->nullable();
            $table->string('billing_neighborhood')->nullable();
            $table->string('billing_postal_code')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donor_fiscal_records');
    }
};
