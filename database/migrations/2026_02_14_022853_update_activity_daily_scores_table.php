<?php

use App\Models\Activity;
use App\Models\ActivityPlan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activity_daily_scores', function (Blueprint $table) {
            $table->foreignIdFor(ActivityPlan::class)->after('candidate_id')->constrained('activity_plan')->onDelete('cascade');
            $table->dropColumn('activity_id');
            $table->enum('color', ['negative', 'warning', 'positive', 'grey'])->after('score')->default('grey');
            $table->index('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_daily_scores', function (Blueprint $table) {
            //
        });
    }
};
