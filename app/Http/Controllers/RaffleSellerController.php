<?php

namespace App\Http\Controllers;

use App\Models\Raffle;
use App\Models\RaffleSeller;
use Illuminate\Http\Request;

class RaffleSellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $request->filled('phone')
        ? RaffleSeller::wherePhone($request->phone)->firstOrFail()
        : RaffleSeller::all();
        return response()->json(compact('data'));
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
    public function show(RaffleSeller $raffleSeller)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RaffleSeller $raffleSeller)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RaffleSeller $raffleSeller)
    {
        //
    }
}
