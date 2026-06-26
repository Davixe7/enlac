<?php

use App\Enums\CandidateStatus;
use App\Models\Candidate;
use App\Models\Program;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('Actualiza la columna status del candidato a activo cuando llega su fecha de ingreso', function(){
    $currentStatus = CandidateStatus::SCHEDULED;
    $targetStatus  = CandidateStatus::ACTIVE;
    $targetDate    = now()->addMonth()->format('Y-m-d');

    $program = Program::factory()->createOne();
    $candidate = Candidate::factory()->createOne([
        'status'     => $currentStatus,
        'entry_date' => $targetDate
    ]);

    $this->artisan('candidates:activate-programmed')->assertSuccessful();

    expect($candidate->status)->toBe($currentStatus);

    $this->travelTo($targetDate);
    $this->artisan('candidates:activate-programmed')->assertSuccessful();
    expect($candidate->fresh()->status)->toBe($targetStatus);
});
