<?php

use App\Enums\CandidateStatus;
use App\Models\Candidate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Crear la tabla de LOGS (Historial)
        Schema::create('candidate_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->onDelete('cascade');
            $table->string('status');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->text('comments')->nullable();
            $table->timestamps();
        });

        // 2. Añadir la nueva columna de status (string) a la tabla candidates
        Schema::table('candidates', function (Blueprint $table) {
            $table->string('status')->after('candidate_status_id')->nullable();
        });

        $candidates = Candidate::with([
            'candidateStatus',
            'evaluations' => function ($query) {
                $query->latest();
            }
        ])->get();

        foreach ($candidates as $candidate) {
            $lastEvaluation = $candidate->evaluations->first();

            if ($candidate->candidate_status_id > 1 && $lastEvaluation) {
                $newStatus = $candidate->candidate_status_id == 2
                    ? CandidateStatus::REJECTED
                    : CandidateStatus::ACCEPTED;

                $candidate->updateStatus(
                    $newStatus,
                    "Generado automaticamente desde la evaluacion",
                    $lastEvaluation->created_at
                );
            }

            if ($candidate->candidate_status_id > 3) {
                $candidate->updateStatus(
                    CandidateStatus::from($candidate->candidateStatus->name),
                    "Generado automaticamente desde la evaluacion"
                );
            }

            if ($candidate->candidate_status_id == 1) {
                $candidate->update(['status' => CandidateStatus::PENDING]);
            }
        }
    }

    public function down(): void
    {
        // El proceso inverso es complejo, usualmente en estas migraciones 
        // estructurales se recomienda hacer backup antes de ejecutar.
    }
};
