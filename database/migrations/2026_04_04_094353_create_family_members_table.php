<?php

use App\Models\Candidate;
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
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Candidate::class)->constrained()->onDelete('cascade');
            // Nombre completo
            $table->string('name');

            // Edad (unsigned para que no sea negativa)
            $table->unsignedTinyInteger('age')->nullable();

            // Parentesco, Estado Civil, Escolaridad y Ocupación
            $table->string('relationship')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('scolarship')->nullable();
            $table->string('ocupation')->nullable();

            // Ingresos y Egresos (Decimal es mejor para dinero que float)
            // 12 dígitos en total, 2 para decimales (ej: 999,999,999.99)
            $table->decimal('monthly_income', 12, 2)->default(0);
            $table->decimal('monthly_contribution', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};
