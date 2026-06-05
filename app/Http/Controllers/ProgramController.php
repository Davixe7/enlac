<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProgramResource;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $programs = Program::where('is_active', true)
            ->where(function ($query) use ($today) {
                $query->where('valid_since', '<=', $today) // Ya entró en vigor
                    ->orWhereNull('valid_since');        // O no requiere fecha específica
            })
            ->orderBy('order')
            ->get();

        return ProgramResource::collection($programs);
    }

    public function adminIndex()
    {
        $programs = Program::orderBy('order')->get();
        return ProgramResource::collection($programs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'is_active'   => 'sometimes|boolean',
            'order'       => 'sometimes|integer',
            'valid_since' => 'nullable|date_format:Y-m-d',
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
            'order'       => 'sometimes|integer',
            'valid_since' => 'sometimes|date_format:Y-m-d',
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
