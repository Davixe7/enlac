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
        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name', 191);
            $table->string('last_name', 191);
            $table->string('second_last_name', 191);
            $table->string('company_name')->nullable();
            $table->date('birthdate');
            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->enum('contact_by', ['enlac', 'parent'])->default('parent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsors');
    }
};
