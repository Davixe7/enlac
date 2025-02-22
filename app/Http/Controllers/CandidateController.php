<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Http\Resources\CandidateResource;
use App\Services\CandidateService;
use App\Http\Requests\UpdateCandidateRequest;
use App\Http\Requests\CreateCandidateRequest;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    protected $candidateService;

    public function __construct(CandidateService $candidateService)
    {
        $this->candidateService = $candidateService;
    }

    public function index()
    {
        return CandidateResource::collection(Candidate::all());
    }

    public function store(Request $request)
    {
        $candidate = $this->candidateService->createCandidate($request);
        return new CandidateResource($candidate->load('evaluation_schedules.evaluator'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Candidate $candidate)
    {
        return new CandidateResource($candidate->load(['contacts.addresses', 'evaluation_schedules.evaluator']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateCandidateRequest $request, Candidate $candidate)
    {
        $candidate = $this->candidateService->updateCandidate($candidate, $request);
        return new CandidateResource($candidate);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Candidate $candidate)
    {
        $candidate = $candidate->delete();
        return response()->json(['data' => $candidate]);
    }
}
