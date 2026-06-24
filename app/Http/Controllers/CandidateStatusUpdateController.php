<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Enums\CandidateStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CandidateStatusUpdateController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Candidate $candidate)
    {
        $data = $request->validate([
            'status'     => ['required', 'string', Rule::in(CandidateStatus::cases())],
            'entry_date' => 'required_if:status,programado',
            'program_id' => 'required_if:status,programado'
        ]);

        $statusEnum = CandidateStatus::from($data['status']);
        $candidate->updateStatus($statusEnum);

        if( !$request->filled(['entry_date', 'program_id']) ){
            return response()->json([], 200);
        }

        $candidate->update($request->only(['entry_date', 'program_id']));
        //Notificar ingreso programado

        return response()->json([], 200);
    }
}
