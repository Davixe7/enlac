<?php

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
        Schema::table('attendances', function (Blueprint $table) {
            $table->renameColumn('work_area_id', 'plan_category_id');
            $table->foreign('plan_category_id')->references('id')->on('plan_categories');
            $table->enum('type', ['area', 'daily'])->default('area');
            $table->string('absence_justification_type')->nullable();
            $table->string('absence_justification_comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('absence_justification_type');
        });
    }
};
