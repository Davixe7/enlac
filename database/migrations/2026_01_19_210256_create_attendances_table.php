<?php

use App\Models\Candidate;
use App\Models\PlanCategory;
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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Candidate::class);
            $table->foreignIdFor(PlanCategory::class)->nullable();
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late'])->default('absent');
            $table->string('absence_justification_type')->nullable();
            $table->string('absence_justification_comment')->nullable();
            $table->enum('type', ['daily', 'area'])->default('area');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
