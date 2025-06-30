<?php

namespace App\Http\Controllers;

use App\Http\Resources\BeneficiaryResource;
use App\Models\Beneficiary;
use App\Models\Candidate;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $beneficiaries = Candidate::beneficiaries()->name($request->name)->get();
        return BeneficiaryResource::collection($beneficiaries);
    }

    /**
     * Display the specified resource.
     */
    public function show(Candidate $candidate)
    {
        return new BeneficiaryResource($candidate);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Candidate $candidate)
    {
        //
    }
}
