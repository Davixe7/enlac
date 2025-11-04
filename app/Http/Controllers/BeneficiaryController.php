<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTransportRequest;
use App\Http\Resources\BeneficiaryResource;
use App\Models\Beneficiary;
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
        $beneficiaries = Candidate::beneficiaries()->name($request->name)->orderBy('first_name')->get();
        return BeneficiaryResource::collection($beneficiaries);
    }

    /**
     * Display the specified resource.
     */
    public function show(Candidate $candidate)
    {
        return new BeneficiaryResource($candidate->load(['personal_groups']));
    }

    public function updateEquineTherapyPermissions(Candidate $candidate, Request $request){
        $data = $request->only(['equinetherapy_permission_medical', 'equinetherapy_permission_legal_guardian']);
        $candidate->update($data);
        return response()->json([], 200);
    }

}
