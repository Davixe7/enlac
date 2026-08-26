<?php

namespace App\Http\Controllers;

use App\Models\BoteoPublishedAmount;
use Illuminate\Http\Request;

class BoteoPublishedAmountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = BoteoPublishedAmount::whereProcurationActivityId($request->procuration_activity_id);
        if( $request->amount ){
            $data = $data->sum('amount');
            return response()->json(compact('data'));
        }
        $data = $data->get();
        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'procuration_activity_id' => 'exists:procuration_activities,id',
            'amount'                  => 'required'
        ]);

        $data = BoteoPublishedAmount::create($data);
        return response()->json(compact('data'));
    }

    /**
     * Display the specified resource.
     */
    public function show(BoteoPublishedAmount $boteoPublishedAmount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BoteoPublishedAmount $boteoPublishedAmount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BoteoPublishedAmount $boteoPublishedAmount)
    {
        //
    }
}
