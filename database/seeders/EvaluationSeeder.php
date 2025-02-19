<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Evaluation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $candidates = Candidate::all();

        foreach ($candidates as $candidate) {
            Evaluation::create([
                'candidate_id' => $candidate->id,
            ]);
        }
    }
}
