<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSponsorRequest;
use App\Http\Requests\UpdateSponsorRequest;
use App\Http\Resources\SponsorResource;
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
        return SponsorResource::collection( $sponsors );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSponsorRequest $request)
    {
        $sponsor = Sponsor::create($request->validated());
        return response()->json(['data' => $sponsor]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sponsor $sponsor)
    {
        return new SponsorResource( $sponsor );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSponsorRequest $request, Sponsor $sponsor)
    {
        $sponsor->update($request->validated());
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
