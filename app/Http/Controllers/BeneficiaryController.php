<?php

namespace App\Http\Controllers;

use App\Http\Resources\BeneficiaryReportsResource;
use App\Http\Resources\BeneficiaryResource;
use App\Models\Candidate;
use App\Services\CandidateService;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{

    protected $candidateService;

    public function __construct(CandidateService $candidateService)
    {
        $this->candidateService = $candidateService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $beneficiaries = Candidate::name($request->name)
            ->with(['program'])
            ->beneficiaries();

        if( $request->equinetherapy == 1 ){
            $beneficiaries = $beneficiaries->equinetherapyActivePlan();
        }

        $beneficiaries = $beneficiaries->orderBy('first_name')->get();

        return BeneficiaryResource::collection($beneficiaries);
    }
 
    public function show(Candidate $candidate)
    {
        return new BeneficiaryResource($candidate->load(['program','personal_groups', 'locationDetail']));
    }

    public function updateEquineTherapyPermissions(Candidate $candidate, Request $request){
        $data = $request->only(['equinetherapy_permission_medical', 'equinetherapy_permission_legal_guardian']);
        $candidate->update($data);
        return response()->json([], 200);
    }

    public function reingreso(Candidate $candidate, Request $request)
    {
        $comment = $request->input('comment', 'Reingreso desde reporte');
        $candidate->changeStatus('activo', $comment);

        return new BeneficiaryResource($candidate->load(['statusHistory', 'personal_groups', 'program']));
    }

    public function reports(Request $request){

        $beneficiaries = Candidate::whereIn('candidate_status_id', [7,8,9])
            ->orderBy('first_name', 'ASC')
            ->with(['program', 'candidateStatus'])
            ->get();

        $counts = $beneficiaries
        ->groupBy('candidate_status_id')
        ->map(fn ($group) => $group->count());

        return new BeneficiaryReportsResource([
            'beneficiaries' => $beneficiaries,
            'counts' => $counts,
        ]);
    }
}
