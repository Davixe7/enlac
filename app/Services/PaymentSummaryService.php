<?php

namespace App\Services;

use App\Models\Candidate;
use Illuminate\Http\Request;

class PaymentSummaryService
{
    public function index(Request $request)
    {
        $candidate = Candidate::find( $request->candidate_id );
    }
}
