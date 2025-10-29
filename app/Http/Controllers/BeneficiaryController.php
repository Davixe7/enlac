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


    public function update(UpdateTransportRequest $request, Candidate $candidate){
        $updated = $this->candidateService->updateTransport($candidate, $request->validated());

        return response()->json([
            'message' => 'Datos de transporte actualizados correctamente',
            'data' => new BeneficiaryResource($updated)
        ]);
    }

}
