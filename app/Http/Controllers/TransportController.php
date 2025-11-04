<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTransportRequest;
use App\Http\Resources\BeneficiaryResource;
use App\Models\Candidate;
use App\Services\CandidateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransportController extends Controller
{
    protected $candidateService;

    public function __construct()
    {
        $this->candidateService = new CandidateService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function update(UpdateTransportRequest $request, Candidate $candidate){
        $data = $request->validated();
        $candidate = DB::transaction(function () use ($candidate, $data) {
            $candidate->update([
                'requires_transport'      => $data['requires_transport'],
                'transport_address'       => $data['transport_address'] ?? null,
                'transport_location_link' => $data['transport_location_link'] ?? null,
                'curp'                    => $data['curp'] ?? null,
            ]);

            return $candidate;
        });

        return new BeneficiaryResource( $candidate );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Candidate $candidate)
    {
        $candidate->update(['requires_transport' => false]);
        return response()->json([], 200);
    }
}
