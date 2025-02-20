<?php

namespace App\Http\Controllers;

use App\Http\Resources\EvaluationResource;
use App\Models\Candidate;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEvaluationRequest;
use App\Http\Requests\UpdateEvaluationRequest;
use App\Models\User;

class EvaluationController extends Controller
{
    public function store(StoreEvaluationRequest $request, User $user)
    {
        $candidate = Candidate::where('id', $request->validated()->candidate_id);
        $evaluation = $candidate->evaluation()->create($request->validated());

        return new EvaluationResource($evaluation);
    }

    public function show(Evaluation $evaluation)
    {
        return new EvaluationResource($evaluation);
    }

    public function update(UpdateEvaluationRequest $request, Evaluation $evaluation)
    {
        $evaluation = $evaluation->update($request->validated());
        return new EvaluationResource($evaluation);
    }

    public function destroy(Evaluation $evaluation)
    {
        $evaluation->delete();
        return response()->json(['data' => $evaluation]);
    }
}
