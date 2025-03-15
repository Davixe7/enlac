<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Http\Resources\CandidateResource;
use App\Services\CandidateService;
use App\Http\Requests\UpdateCandidateRequest;
use App\Http\Requests\CreateCandidateRequest;
use App\Http\Resources\CandidateResults;
use App\Http\Resources\CandidateResultsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    protected $candidateService;

    public function __construct(CandidateService $candidateService)
    {
        $this->candidateService = $candidateService;
    }

    public function index(Request $request)
    {
        $candidates = Candidate::name($request->name)
        ->birthDate($request->birth_date)
        ->evaluationBetween($request->date_from, $request->date_to)->get();

        $counts = $candidates->countBy(function ($candidate) {
            if ($candidate->acceptance_status === null) {
                return 'en_proceso';
            } elseif ($candidate->acceptance_status === 0) {
                return 'rechazados';
            } elseif ($candidate->acceptance_status === 1 && $candidate->onboard_at == null) {
                return 'aceptados_no_ingresados';
            } elseif ($candidate->acceptance_status === 1 && $candidate->onboard_at != null) {
                return 'aceptados_ingresados';
            }
        });

        $counts = [
            'en_proceso' => $counts->get('en_proceso', 0),
            'rechazados' => $counts->get('rechazados', 0),
            'aceptados_no_ingresados' => $counts->get('aceptados_no_ingresados', 0),
            'aceptados_ingresados' => $counts->get('aceptados_ingresados', 0),
            'total' => $candidates->count()
        ];

        return CandidateResource::collection($candidates)->additional(['counts' => $counts]);
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
        $this->candidateService->updateCandidate($candidate, $request);
        return new CandidateResource($candidate);
    }

    public function admission(Request $request, Candidate $candidate){
        $candidate->update([
            'acceptance_status' => $request->acceptance_status,
            'rejection_comment' => $request->rejection_comment,
            'program_id'        => $request->program_id
        ]);

        return new CandidateResource($candidate);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Candidate $candidate)
    {
        $candidate->delete();
        return response()->json(['data' => $candidate]);
    }
}
