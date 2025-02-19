<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrainFunctionResource;
use App\Models\BrainFunction;
use App\Http\Requests\StoreBrainFunctionRequest;
use App\Http\Requests\UpdateBrainFunctionRequest;

class BrainFunctionController extends Controller
{
    public function index()
    {
        return BrainFunctionResource::collection(BrainFunction::all());
    }

    public function store(StoreBrainFunctionRequest $request)
    {
        $brainFunction = BrainFunction::create($request->validated());
        return new BrainFunctionResource($brainFunction);
    }

    public function show(BrainFunction $brainFunction)
    {
        return new BrainFunctionResource($brainFunction);
    }

    public function update(UpdateBrainFunctionRequest $request, BrainFunction $brainFunction)
    {
        $brainFunction->update($request->validated());
        return new BrainFunctionResource($brainFunction);
    }

    public function destroy(BrainFunction $brainFunction)
    {
        $brainFunction->delete();
        return response()->json(['data' => $brainFunction]);
    }
}
