<?php

use App\Enums\CandidateStatus;
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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->date('birth_date');
            $table->text('diagnosis');
            $table->text('review')->nullable();
            $table->unsignedBigInteger('sheet');
            $table->string('info_channel');
            $table->boolean('requires_transport')->default(false);
 
            $table->string('status')->default(CandidateStatus::PENDING);
            $table->string('admission_comment')->default(null)->nullable();
            $table->boolean('equinetherapy_permission_medical')->default(false);
            $table->boolean('equinetherapy_permission_legal_guardian')->default(false);
            $table->date('entry_date')->default(null)->nullable();
            $table->foreignId('program_id')->nullable()->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
