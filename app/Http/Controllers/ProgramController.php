<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProgramResource;
use App\Models\Program;
use App\Models\ProgramPrice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $programs = Program::whereHas('latestLog', function($query){
            $query->where('is_active', 1);
        })->get();

        return ProgramResource::collection($programs);
    }

    public function adminIndex()
    {
        $programs = Program::orderBy('order')
        ->get();
        return ProgramResource::collection($programs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'is_active'   => 'sometimes|boolean',
            'order'       => 'sometimes|integer',
        ]);

        // Asignamos un orden por defecto
        if (!isset($validated['order'])) {
            $validated['order'] = Program::max('order') + 1;
        }

        $program = Program::create($validated);

        return new ProgramResource($program);
    }

    public function show(Program $program)
    {
        return new ProgramResource($program->load(['plan', 'plan_type', 'activities']));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'price'       => 'sometimes|numeric',
            'is_active'   => 'sometimes|boolean',
            'order'       => 'sometimes|integer'
        ]);

        $program->update($validated);

        return new ProgramResource($program);
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return response()->json(['message' => 'Program deleted successfully']);
    }
}
