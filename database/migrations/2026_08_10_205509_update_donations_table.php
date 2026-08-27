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
        Schema::table('donations', function (Blueprint $table) {
            $table->foreignId('radiomarathon_key_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('boteo_responsible_name')->nullable();
            $table->string('boteo_counter_name')->nullable();
            $table->string('source')->nullable();
            $table->string('donation_type')->nullable();
            $table->string('donor_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('radiomarathon_key_id');
            $table->dropColumn('boteo_responsible_name');
            $table->dropColumn('boteo_counter_name');
            $table->dropColumn('source');
            $table->dropColumn('donation_type');
            $table->dropColumn('donor_name');
        });
    }
};
