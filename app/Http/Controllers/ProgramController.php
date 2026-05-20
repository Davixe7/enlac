<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProgramResource;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::where('is_active', true)->orderBy('order')->get();
        return ProgramResource::collection($programs);
    }

    public function adminIndex()
    {
        $programs = Program::orderBy('order')->get();
        return ProgramResource::collection($programs);
    }

    public function store(Request $request, Program $program)
    {
        return new ProgramResource($program);
    }

    public function show(Program $program)
    {
        return new ProgramResource($program->load(['plan', 'plan_type', 'activities']));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'name'        => 'sometimes|string',
            'price'       => 'sometimes|numeric',
            'is_active'   => 'sometimes|boolean',
            'order'       => 'sometimes|integer',
            'valid_since' => 'sometimes|date_format:Y-m-d', // Cambiado a valid_since
        ]);

        $program->update($request->except('valid_since'));

        return new ProgramResource($program);
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return response()->json(['message' => 'Program deleted successfully']);
    }
}
