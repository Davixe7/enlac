<?php

namespace App\Http\Controllers;

use App\Models\BrainFunctionRank;
use App\Http\Resources\BrainFunctionRankResource;
use App\Http\Requests\StoreBrainFunctionRankRequest;
use App\Http\Requests\UpdateBrainFunctionRankRequest;
use Illuminate\Http\Resources\Json\JsonResource;

class BrainFunctionRankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResource
    {
        $brainFunctionRanks = BrainFunctionRank::all();
        return BrainFunctionRankResource::collection($brainFunctionRanks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrainFunctionRankRequest $request): JsonResource
    {
        $brainFunctionRank = BrainFunctionRank::create($request->validated());
        return new BrainFunctionRankResource($brainFunctionRank);
    }

    /**
     * Display the specified resource.
     */
    public function show(BrainFunctionRank $brainFunctionRank): JsonResource
    {
        return new BrainFunctionRankResource($brainFunctionRank);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrainFunctionRankRequest $request, BrainFunctionRank $brainFunctionRank): JsonResource
    {
        $brainFunctionRank->update($request->validated());
        return new BrainFunctionRankResource($brainFunctionRank);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BrainFunctionRank $brainFunctionRank): JsonResource
    {
        $brainFunctionRank->delete();
        return new BrainFunctionRankResource($brainFunctionRank);
    }
}
