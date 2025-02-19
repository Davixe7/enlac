<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrainLevelResource;
use App\Models\BrainLevel;
use App\Http\Requests\StoreBrainLevelRequest;
use App\Http\Requests\UpdateBrainLevelRequest;

class BrainLevelController extends Controller
{
    public function index()
    {
        return BrainLevelResource::collection(BrainLevel::all());
    }

    public function store(StoreBrainLevelRequest $request)
    {
        $brainLevel = BrainLevel::create($request->validated());
        return new BrainLevelResource($brainLevel);
    }

    public function show(BrainLevel $brainLevel)
    {
        return new BrainLevelResource($brainLevel);
    }

    public function update(UpdateBrainLevelRequest $request, BrainLevel $brainLevel)
    {
        $brainLevel->update($request->validated());
        return new BrainLevelResource($brainLevel);
    }

    public function destroy(BrainLevel $brainLevel)
    {
        $brainLevel->delete();
        return response()->json(['data' => $brainLevel]);
    }
}
