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
        Schema::create('donor_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained()->onDelete('cascade');
            $table->date('visit_date');
            $table->foreignId('enlac_user_id')->constrained('users')->onDelete('restrict');
            $table->string('recommended_by')->nullable();
            $table->string('reason'); // Motivo de la visita *
            $table->string('schedule_contact_name')->nullable();
            $table->string('schedule_contact_phone', 20)->nullable();
            $table->string('received_by')->nullable();
            $table->text('visitors_names')->nullable();
            $table->text('material_presented')->nullable();
            $table->text('result')->nullable();
            $table->text('comments')->nullable();
            $table->text('interests_hobbies')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_visits');
    }
};
