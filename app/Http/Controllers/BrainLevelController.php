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
        $braindlevels = BrainLevel::query()
            ->orderByRaw(
                'CASE grade
                    WHEN ? THEN ?
                    WHEN ? THEN ?
                    WHEN ? THEN ?
                    WHEN ? THEN ?
                    WHEN ? THEN ?
                    WHEN ? THEN ?
                    WHEN ? THEN ?
                    ELSE ?
                END DESC',
                ['VII', 7, 'VI', 6, 'V', 5, 'IV', 4, 'III', 3, 'II', 2, 'I', 1, 0]
            )
            ->get();

        return BrainLevelResource::collection($braindlevels);
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
