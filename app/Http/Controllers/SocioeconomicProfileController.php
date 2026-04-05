<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSocioeconomicProfile;
use App\Models\SocioeconomicProfile;
use Illuminate\Http\Request;

class SocioeconomicProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if( $request->filled('candidate_id') ){
            $data = SocioeconomicProfile::whereCandidateId($request->candidate_id)->first();
            return response()->json(compact('data'));
        }

        $data = SocioeconomicProfile::all();
        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSocioeconomicProfile $request)
    {
        $data = $request->validated();
        $data['date'] = now();
        $data = SocioeconomicProfile::create($data);
        return response()->json($data, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(SocioeconomicProfile $socioeconomicProfile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreSocioeconomicProfile $request, SocioeconomicProfile $socioeconomicProfile)
    {
        $data = $request->validated();
        $socioeconomicProfile->update($data);
        return response()->json($socioeconomicProfile, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SocioeconomicProfile $socioeconomicProfile)
    {
        //
    }
}
