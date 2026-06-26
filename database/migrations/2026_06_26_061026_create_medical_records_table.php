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
        Schema::create('medical_records', function (Blueprint $table) {
            // Clave primaria autoincrementable bigint UNSIGNED
            $table->id('id_medical_record');

            // Campos de enteros indexables/relacionales (pueden ser nulos por defecto según tu SQL)
            $table->foreignId('candidate_id')->nullable();
            $table->foreignId('appointment_id')->nullable();
            $table->integer('type_id')->nullable()->comment('Work Area id');
            $table->integer('appointment_type')->nullable()->comment('0=Historia Clinica; 1=Seguimiento');

            // Fecha obligatoria
            $table->date('date_medical_record');

            // Historiales médicos y antecedentes (Textos largos con codificación unicode)
            $table->text('hereditary_family_history')->nullable();
            $table->text('non_pathological_personal_history')->nullable();
            $table->text('perinatal_history')->nullable();
            $table->text('andrological_gynecological_obstetric_history')->nullable();
            $table->text('medical_history')->nullable();
            $table->text('psychiatric_mental_status')->nullable();

            // Sistemas corporales
            $table->text('nervous_system')->nullable();
            $table->text('respiratory_system')->nullable();
            $table->text('cardiovascular_system')->nullable();
            $table->text('digestive_system')->nullable();
            $table->text('genitourinary_system')->nullable();
            $table->text('musculoskeletal_system')->nullable();
            $table->text('endocrine_system')->nullable();
            $table->text('sensory_system')->nullable();
            $table->text('integumentary_system')->nullable();

            // Signos vitales y somatometría básica (Están declarados como TEXT en tu SQL original)
            $table->text('weight')->nullable();
            $table->text('height')->nullable();
            $table->text('head_circumference')->nullable();
            $table->text('heart_rate')->nullable();
            $table->text('respiratory_rate')->nullable();
            $table->text('temperature')->nullable();

            // Inspección general y regiones
            $table->text('general_inspection')->nullable();
            $table->text('head')->nullable();
            $table->text('mental_status')->nullable();
            $table->text('hair')->nullable();
            $table->text('neck')->nullable();
            $table->text('thorax')->nullable();
            $table->text('abdomen')->nullable();
            $table->text('genitalia')->nullable();
            $table->text('anorectal')->nullable();
            $table->text('spine')->nullable();
            $table->text('upper_lower_limbs')->nullable();
            $table->text('peripheral_vascular_system')->nullable();
            $table->text('skin_appendages')->nullable();
            $table->text('areas_dryness_excessive_sweating')->nullable();

            // Diagnóstico y análisis
            $table->text('diagnostic_impression')->nullable();
            $table->text('treatment')->nullable();
            $table->text('case_analysis')->nullable();

            // Estado con valor por defecto
            $table->integer('status')->default(1)->comment('0: inactivo, 1: activo');

            // Métricas nutricionales / Antropometría avanzada (TEXT en SQL)
            $table->text('initial_weight')->nullable();
            $table->text('weight_age')->nullable();
            $table->text('height_age')->nullable();
            $table->text('weight_height')->nullable();
            $table->text('waist_cm')->nullable();
            $table->text('hip_cm')->nullable();
            $table->text('chest_cm')->nullable();
            $table->text('brain_perimeter_cm')->nullable();
            $table->text('brachial_circumference_cm')->nullable();
            $table->text('wrist_circumference_cm')->nullable();
            $table->text('calf_circumference_cm')->nullable();
            $table->text('other')->nullable();
            $table->text('imc')->nullable();

            // Campos de metodología SOAP médica
            $table->text('subjective')->nullable();
            $table->text('objective')->nullable();
            $table->text('assessment')->nullable();
            $table->text('plan')->nullable();
            $table->text('date_soap')->nullable();

            // Timestamps de Laravel (created_at y updated_at nulos por defecto)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
