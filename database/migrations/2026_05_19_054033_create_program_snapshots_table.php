<?php

use App\Models\Program;
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
        Schema::create('program_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Program::class)->constrained()->onDelete('cascade');
            $table->decimal('price');
            $table->date('valid_since');
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_snapshots');
    }
};
