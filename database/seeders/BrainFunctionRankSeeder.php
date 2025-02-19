<?php

namespace Database\Seeders;

use App\Models\BrainFunction;
use App\Models\BrainFunctionRank;
use App\Models\BrainLevel;
use App\Models\Candidate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrainFunctionRankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brainLevelIds = BrainLevel::pluck('id');
        $brainFunctionIds = BrainFunction::pluck('id');
        $candidateIds = Candidate::pluck('id');

        $data = [
            [
                'caracteristic' => 'F',
                'comments' => 'Comentario 1',
                'laterality_impact' => 'l',
                'brain_level_id' => $brainLevelIds->random(),
                'brain_function_id' => $brainFunctionIds->random(),
                'candidate_id' => $candidateIds->random(),
            ],
            [
                'caracteristic' => 'P',
                'comments' => 'Comentario 2',
                'laterality_impact' => 'r',
                'brain_level_id' => $brainLevelIds->random(),
                'brain_function_id' => $brainFunctionIds->random(),
                'candidate_id' => $candidateIds->random(),
            ],
            [
                'caracteristic' => '0',
                'comments' => 'Comentario 3',
                'laterality_impact' => 'l',
                'brain_level_id' => $brainLevelIds->random(),
                'brain_function_id' => $brainFunctionIds->random(),
                'candidate_id' => $candidateIds->random(),
            ],
            [
                'caracteristic' => 'F',
                'comments' => 'Comentario 4',
                'laterality_impact' => 'r',
                'brain_level_id' => $brainLevelIds->random(),
                'brain_function_id' => $brainFunctionIds->random(),
                'candidate_id' => $candidateIds->random(),
            ],
            [
                'caracteristic' => 'P',
                'comments' => 'Comentario 5',
                'laterality_impact' => 'l',
                'brain_level_id' => $brainLevelIds->random(),
                'brain_function_id' => $brainFunctionIds->random(),
                'candidate_id' => $candidateIds->random(),
            ],
        ];

        foreach ($data as $item) {
            BrainFunctionRank::create($item);
        }
    }
}
