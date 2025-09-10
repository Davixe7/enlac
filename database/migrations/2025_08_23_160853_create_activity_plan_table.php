<?php

use App\Models\Activity;
use App\Models\Plan;
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
        Schema::create('activity_plan', function (Blueprint $table) {
            $table->foreignIdFor(Activity::class);
            $table->foreignIdFor(Plan::class);
            $table->string('daily_goal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_plan');
    }
};
