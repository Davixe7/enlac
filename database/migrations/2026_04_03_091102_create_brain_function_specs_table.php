<?php

use App\Models\BrainFunction;
use App\Models\BrainLevel;
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
        Schema::create('brain_function_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(BrainLevel::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(BrainFunction::class)->constrained()->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brain_function_specs');
    }
};
