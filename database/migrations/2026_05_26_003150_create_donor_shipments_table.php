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
        Schema::create('donor_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->string('material_description');
            $table->string('delivery_method');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_shipments');
    }
};
