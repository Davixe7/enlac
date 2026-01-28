<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Activity;
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
            $table->foreignIdFor(Activity::class);
            $table->foreignIdFor(Candidate::class);
            $table->string('score');
            $table->date('date');
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
