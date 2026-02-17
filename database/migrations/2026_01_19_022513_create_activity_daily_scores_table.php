<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Activity;
use App\Models\ActivityPlan;
use App\Models\Candidate;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_daily_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ActivityPlan::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Candidate::class)->constrained()->onDelete('cascade');
            $table->date('date');
            $table->string('score');
            $table->enum('color', ['negative', 'warning', 'positive', 'grey'])->default('grey');
            $table->index('color');
            $table->boolean('closed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_daily_scores');
    }
};
