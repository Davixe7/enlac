<?php

use App\Models\Candidate;
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
        Schema::table('payment_configs', function (Blueprint $table) {
            $table->dropForeign(['candidate_id']);
        });
        Schema::rename('payment_configs', 'sponsorships');
        Schema::table('sponsorships', function (Blueprint $table) {
            $table->foreign('candidate_id')->references('id')->on('candidates');
        });

        Schema::table('payment_config_snapshots', function (Blueprint $table) {
            $table->dropForeign(['payment_config_id']);
            $table->renameColumn('payment_config_id', 'sponsorship_id');
        });
        Schema::rename('payment_config_snapshots', 'payment_configs');
        Schema::table('payment_configs', function (Blueprint $table) {
            $table->foreign('sponsorship_id')->references('id')->on('sponsorships');
            $table->foreignId('candidate_id')->references('id')->on('candidates');
            $table->unsignedBigInteger('sponsor_id')->nullable();
        });

        Schema::table('deductible_receipts', function (Blueprint $table) {
            //$table->dropForeign(['payment_config_id']);
            $table->renameColumn('payment_config_id', 'sponsorship_id');
            $table->foreign('sponsorship_id')->references('id')->on('sponsorships');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('payment_configs', 'payment_config_snapshots');
        Schema::rename('sponsorships', 'payment_configs');
        Schema::table('deductible_receipts', function (Blueprint $table) {
            $table->dropForeign(['sponsorship_id']);
            $table->renameColumn('sponsorship_id', 'payment_config_id');
            $table->foreign('payment_config_id')->references('id')->on('payment_configs');
        });
    }
};
