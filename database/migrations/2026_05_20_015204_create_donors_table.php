<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donors', function (Blueprint $table) {
            $table->id();

            // Personal Data
            $table->string('first_name');
            $table->string('last_name');
            $table->string('second_last_name')->nullable();
            $table->string('preferred_name')->nullable();
            $table->enum('marital_status', ['Soltero(a)', 'Casado(a)', 'Divorciado(a)', 'Viudo(a)', 'Unión Libre', 'Desconocido'])->default('Desconocido');
            $table->enum('gender', ['Masculino', 'Femenino'])->nullable();
            $table->date('birth_date')->nullable();
            $table->string('cellphone', 10);
            $table->string('landline')->nullable();
            $table->string('personal_email')->nullable();
            $table->boolean('knows_facilities')->default(false);
            $table->string('sector');

            // Spouse Data
            $table->string('spouse_first_name')->nullable();
            $table->string('spouse_last_name')->nullable();
            $table->string('spouse_second_last_name')->nullable();
            $table->date('spouse_birth_date')->nullable();
            $table->string('wedding_anniversary', 5)->nullable(); // MM-DD

            // Address
            $table->string('street')->nullable();
            $table->string('exterior_number')->nullable();
            $table->string('neighborhood')->nullable(); // Colonia
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->default('México');

            // Work Data
            $table->string('company_name')->nullable();
            $table->string('job_title')->nullable();

            // Contact Settings & Status
            $table->text('contact_restrictions');
            $table->string('referred_by')->nullable();
            $table->string('referral_relationship')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_private_contact')->default(false);
            $table->string('prospect_for')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('status_changed_at')->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donors');
    }
};
