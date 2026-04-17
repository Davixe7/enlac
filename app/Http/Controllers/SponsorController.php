<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSponsorRequest;
use App\Http\Requests\UpdateSponsorRequest;
use App\Http\Resources\SponsorResource;
use App\Models\Sponsor;
use App\Models\SponsorAddress;
use Illuminate\Http\Request;
    use App\Exports\SponsorExport;
use Maatwebsite\Excel\Facades\Excel;

class SponsorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sponsors = Sponsor::byCandidate( $request->candidate_id )->orderBy('name')->get();
        return SponsorResource::collection( $sponsors );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSponsorRequest $request)
    {
        $data = $request->validated();
        unset($data['addresses']);

        $sponsor = Sponsor::create($data);

        if( $request->hasFile('profilePicture') ){
            $sponsor->addMediaFromRequest('profilePicture')->toMediaCollection('profile_picture');
        }

        if( $request->addresses ){
            foreach( $request->addresses as $address ){
                $sponsor->addresses()->create($address);
            }
        }
        return new SponsorResource($sponsor);
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

        if( $request->hasFile('profilePicture') ){
            $sponsor->addMediaFromRequest('profilePicture')->toMediaCollection('profile_picture');
        }

        if( $request->addresses ){
            foreach( $request->addresses as $address ){
                SponsorAddress::updateOrCreate(['sponsor_id'=>$sponsor->id, 'type'=>$address['type']], $address);
            }
        }

        return new SponsorResource($sponsor);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sponsor $sponsor)
    {
        //
    }

    public function export()
    {
        $query = Sponsor::query();

        $fileName = 'reporte_general_padrinos_' . now()->format('d-m-Y_His') . '.xlsx';

        $response = Excel::download(new SponsorExport($query), $fileName);

        //El header Access-Control-Expose-Headers le da Permiso a Axios para leer el archivo
        $response->headers->set('Access-Control-Expose-Headers', 'Content-Disposition');
        return $response;
    }
}
