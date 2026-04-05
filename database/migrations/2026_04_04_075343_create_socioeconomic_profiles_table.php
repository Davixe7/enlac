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
        Schema::create('socioeconomic_profiles', function (Blueprint $table) {
            $table->id();
            // Relación con el beneficiario (basado en la vista de Admisiones) [1]
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');

            // --- SECCIÓN: Datos del Candidato [2, 3] ---
            $table->date('date');
            $table->string('facility_tour_notes')->nullable(); // "¿Se le dio recorrido?"
            $table->boolean('attends_regular_school')->default(false);
            $table->boolean('previously_attended_regular_school')->default(false);
            $table->string('regular_school_grade')->nullable();
            $table->boolean('attends_specialized_school')->default(false);
            $table->boolean('previously_attended_specialized_school')->default(false);
            $table->decimal('specialized_school_cost', 10, 2)->nullable();
            $table->string('financial_provider_name')->nullable();
            $table->boolean('has_formal_employment')->default(false);
            $table->string('workplace')->nullable();
            $table->text('childcare_provider')->nullable(); // "¿Quién cuida a los hijos?"
            $table->boolean('has_more_children')->default(false);
            $table->text('other_children_details')->nullable(); // "Cuántos y edades"
            $table->text('other_children_schools')->nullable();
            $table->text('other_children_occupations')->nullable();

            // --- SECCIÓN: Datos del Solicitante [3, 4] ---
            $table->string('requester_name');
            $table->string('requester_relationship'); // Drop down: Madre/Padre, etc. [5, 6]
            $table->integer('requester_age');
            $table->date('requester_birth_date');
            $table->string('requester_gender');
            $table->string('requester_marital_status'); // Drop down [7]
            $table->string('requester_phone');
            $table->string('requester_origin'); // "Originario de"

            // Domicilio [4]
            $table->string('address_street');
            $table->string('address_ext_num');
            $table->string('address_colony');
            $table->string('address_zip_code');
            $table->string('address_country');
            $table->string('address_state');
            $table->string('address_city');

            // Salud [4]
            $table->boolean('has_medical_service')->default(false);
            $table->string('medical_institution')->nullable();
            $table->boolean('has_specialized_medical_access')->default(false);
            $table->string('specialized_medical_type')->nullable();

            // --- SECCIÓN: Vivienda [7] ---
            // Usando tu lógica de una sola columna para radio + texto "otro"
            $table->string('wall_material'); // Ladrillo, Block, etc.
            $table->string('roof_material'); // Losa, Lámina, etc.
            $table->string('housing_status'); // Propia, Renta, etc.
            $table->string('bathroom_count');
            $table->string('bedroom_count');

            // Servicios (Booleano por cada uno para facilitar estadísticas) [7]
            $table->boolean('service_water')->default(false);
            $table->boolean('service_drainage')->default(false);
            $table->boolean('service_electricity')->default(false);
            $table->boolean('service_phone')->default(false);
            $table->boolean('service_internet')->default(false);

            $table->boolean('has_vehicle')->default(false);
            $table->string('transport_method')->nullable();

            // --- SECCIÓN: Información Adicional del Hogar [8, 9] ---
            $table->integer('household_members_count');
            $table->boolean('other_disabled_members')->default(false);
            $table->string('disabled_members_type')->nullable();
            $table->integer('disabled_members_count')->nullable();
            $table->string('disabled_members_ages')->nullable();

            $table->boolean('receives_govt_support')->default(false);
            $table->string('govt_support_institution')->nullable();
            $table->decimal('govt_support_amount', 10, 2)->nullable();
            $table->boolean('receives_additional_income')->default(false);
            $table->decimal('additional_income_amount', 10, 2)->nullable();

            // --- SECCIÓN: Gastos Mensuales (Campos monetarios con 2 decimales) [9] ---
            $table->decimal('expense_rent', 10, 2)->default(0);
            $table->decimal('expense_electricity', 10, 2)->default(0);
            $table->decimal('expense_water', 10, 2)->default(0);
            $table->decimal('expense_food', 10, 2)->default(0);
            $table->decimal('expense_special_supplies', 10, 2)->default(0); // "Alimento especial y pañales"
            $table->decimal('expense_phone', 10, 2)->default(0);
            $table->decimal('expense_school', 10, 2)->default(0);
            $table->decimal('expense_gas', 10, 2)->default(0);
            $table->decimal('expense_gasoline', 10, 2)->default(0);
            $table->decimal('expense_medical', 10, 2)->default(0);
            $table->decimal('expense_debts', 10, 2)->default(0);
            $table->decimal('expense_others', 10, 2)->default(0);

            // Resumen Financiero [9, 10]
            $table->decimal('total_expenses', 10, 2);
            $table->decimal('total_income', 10, 2);
            $table->decimal('income_expense_difference', 10, 2);
            $table->text   ('solvency_notes')->nullable();
            $table->string ('economic_level')->nullable();
            $table->decimal('monthly_contribution_amount', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('socioeconomic_profiles');
    }
};
