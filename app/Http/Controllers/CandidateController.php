<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Http\Resources\CandidateResource;
use App\Services\CandidateService;
use App\Http\Requests\StoreCandidateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CandidateController extends Controller
{
    protected $candidateService;

    public function __construct(CandidateService $candidateService)
    {
        $this->candidateService = $candidateService;
    }

    public function dashboard() {
        $candidates = Candidate::whereHas('appointments', function($query){
            $query
            ->where('type_id', 0)
            ->whereEvaluatorId( auth()->id() );
        })
        ->where('admission_status', null)
        ->orderBy('first_name', 'ASC')
        ->get();

        return CandidateResource::collection($candidates);
    }

    public function index(Request $request)
    {
        $candidates = Candidate::name($request->name)
        ->birthDate($request->birth_date)
        ->evaluationBetween($request->date_from, $request->date_to)
        ->orderBy('first_name', 'ASC')
        ->get();

        $counts = $candidates->countBy(function ($u) {
            $status = $u->admission_status;
            if ($status === null)                        return 'en_proceso';
            if ($status === 0)                           return 'rechazados';
            if ($status === 1 && $u->entry_date == null) return 'aceptados_no_ingresados';
            if ($status === 1 && $u->entry_date != null) return 'aceptados_ingresados';
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

    public function store(StoreCandidateRequest $request)
    {
        $candidate = $this->candidateService->createCandidate($request);
        return new CandidateResource($candidate);
    }

    /**
     * Display the specified resource.
     */
    public function show(Candidate $candidate)
    {
        return new CandidateResource($candidate->load(['contacts.addresses', 'interviewee']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCandidateRequest $request, Candidate $candidate)
    {
        $this->candidateService->updateCandidate($candidate, $request);
        return new CandidateResource($candidate);
    }

    public function admission(Request $request, Candidate $candidate){
        $request->validate([
            'admission_comment' => 'required_if:admission_status,0,null,false'
        ]);

        $candidate->update([
            'admission_status'  => $request->admission_status,
            'admission_comment' => $request->admission_comment,
            'program_id'        => $request->program_id
        ]);

        return new CandidateResource($candidate);
    }

    public function kardexes( Candidate $candidate ){
        $list = DB::table('media')
        ->whereModelType('App\Models\Candidate')
        ->whereModelId($candidate->id)
        ->where('collection_name', 'like', "kardex_%")
        ->pluck('collection_name');

        return response()->json(['data'=>$list]);
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
