<?php

namespace App\Http\Resources;

use App\Models\BrainLevel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class EvaluationFields extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $brainLevels = BrainLevel::all();
        $brainLevels = $brainLevels->map(function ($level) use ($request) {
            $level->ranks = DB::table('brain_functions')
                ->leftJoin('brain_function_ranks', function ($join) use ($request, $level) {
                    $join
                        ->on('brain_function_id', '=', 'brain_functions.id')
                        ->where('brain_function_ranks.brain_level_id', '=', $level->id)
                        ->where('candidate_id', '=', $request->candidate_id);
                })
                ->select(
                    'brain_functions.id as brain_function_id',
                    'brain_function_ranks.brain_level_id',
                    'brain_function_ranks.id as id',
                    'brain_function_ranks.comments as comments',
                    'brain_function_ranks.laterality_impact as laterality_impact',
                    'brain_function_ranks.caracteristic as caracteristic',
                )
                ->get();
            $level->ranks = $level->ranks->map(function ($rank) use ($request, $level) {
                $rank->candidate_id = intval($request->candidate_id);
                $rank->brain_level_id = $level->id;
                return $rank;
            });
            $level->ranks = $level->ranks->keyBy('brain_function_id');
            return $level;
        });
        return $brainLevels->toArray();
    }
}
