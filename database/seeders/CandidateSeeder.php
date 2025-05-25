<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Appointment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Candidate::factory()->pending()->count(10)->create()
        ->each(function($candidate){
            Appointment::factory()->pending()->count(1)
            ->create(["candidate_id"=>$candidate->id]);
        });

        Candidate::factory()->accepted()->count(10)->create()
        ->each(function($candidate){
            Appointment::factory()->past()->count(1)
            ->create(["candidate_id"=>$candidate->id]);
        });

        Candidate::factory()->rejected()->count(10)->create()
        ->each(function($candidate){
            Appointment::factory()->past()->count(1)
            ->create(["candidate_id"=>$candidate->id]);
        });
    }
}
