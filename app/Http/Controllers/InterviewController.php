<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Http\Resources\InterviewResource;
use App\Http\Requests\StoreInterviewRequest;
use App\Http\Requests\UpdateInterviewRequest;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function index()
    {
        $interviews = Interview::all();
        return InterviewResource::collection($interviews);
    }

    public function store(StoreInterviewRequest $request)
    {
        $interview = Interview::create($request->validated());
        return new InterviewResource($interview);
    }

    public function show(Interview $interview)
    {
        return new InterviewResource($interview);
    }

    public function update(UpdateInterviewRequest $request, Interview $interview)
    {
        $interview->update($request->validated());
        return new InterviewResource($interview);
    }

    public function destroy(Interview $interview)
    {
        $interview->delete();
        return response()->json(['data' => $interview], 204);
    }
}
