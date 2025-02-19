<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProgramResource;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::all();
        return ProgramResource::collection($programs);
    }

    public function store(Request $request, Program $program)
    {
        return new ProgramResource($program);
    }

    public function show(Program $program)
    {
        return new ProgramResource($program);
    }

    public function update(Request $request, Program $program)
    {
        $program->update($request->all());
        return new ProgramResource($program);
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return response()->json(['message' => 'Program deleted successfully']);
    }
}
