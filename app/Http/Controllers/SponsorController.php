<?php

namespace App\Http\Controllers;

use App\Models\PaymentConfig;
use App\Models\Sponsor;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sponsors = Sponsor::byCandidate( $request->candidate_id )->get();
        return response()->json(['data'=>$sponsors]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validated();
        unset($data['candidate_id']);
        $candidate_id = $request->candidate_id;

        $sponsor = Sponsor::create($request->all());

        if($request->filled('candidate_id')){
            PaymentConfig::create([
                'sponsor_id'   => $sponsor->id,
                'candidate_id' => $candidate_id,
                'amount'       => 0,
                'month_payday' => 1,
                'address_type' => 'home',
                'frequency'    => 1,
            ]);
        }

        return response()->json(['data' => $sponsor]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sponsor $sponsor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sponsor $sponsor)
    {
        $sponsor->update($request->all());
        return response()->json(['data' => $sponsor]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sponsor $sponsor)
    {
        //
    }
}
