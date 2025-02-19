<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Http\Resources\CandidateResource;
use App\Services\CandidateService;
use App\Http\Requests\StoreCandidateRequest;
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

    public function createCandidate(CreateCandidateRequest $request)
    {
        $candidate = $this->candidateService->createCandidate($request);
        $candidate->createCandidate();
        return new CandidateResource($candidate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCandidateRequest $request)
    {
        $candidate = Candidate::create($request->validated());
        return new CandidateResource($candidate);
    }

    /**
     * Display the specified resource.
     */
    public function show(Candidate $candidate)
    {
        return new CandidateResource($candidate);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCandidateRequest $request, Candidate $candidate)
    {
        $candidate = $candidate->update($request->validated());
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
