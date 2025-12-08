<?php

namespace App\Http\Controllers;

use App\Http\Resources\CandidateLocationResource;
use App\Models\CandidateLocation;
use Illuminate\Http\Request;

class CandidateLocationController extends Controller
{
    public function store(Request $request)
    {
        $candidateLocation = CandidateLocation::create([
            'candidate_id'            => $request->candidate_id,
            'transport_address'       => $request->transport_address,
            'transport_location_link' => $request->transport_location_link,
            'curp'                    => $request->curp,
        ]);

        $candidateLocation->candidate->update(['requires_transport'=>1]);

        return new CandidateLocationResource($candidateLocation);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CandidateLocation $candidateLocation)
    {
        $candidateLocation->update([
            'transport_address'       => $request->transport_address,
            'transport_location_link' => $request->transport_location_link,
            'curp'                    => $request->curp,
        ]);

        $candidateLocation->candidate()->update([
            'requires_transport' => true,
        ]);

        return new CandidateLocationResource($candidateLocation);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CandidateLocation $candidateLocation)
    {
        $candidateLocation->candidate->update(['requires_transport'=>0]);
        return response()->json([], 204);
    }
}
