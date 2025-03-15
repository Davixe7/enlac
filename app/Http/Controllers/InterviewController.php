<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Http\Resources\InterviewResource;
use App\Http\Requests\StoreInterviewRequest;
use App\Http\Requests\UpdateInterviewRequest;

class InterviewController extends Controller
{
    public function index()
    {
        if( request()->filled('candidate_id') ){
            $interview = Interview::whereCandidateId(request()->candidate_id)->firstOrFail();
            return new InterviewResource($interview);
        }
        $interviews = Interview::all();
        return InterviewResource::collection($interviews);
    }

    public function store(StoreInterviewRequest $request)
    {
        $data = $request->validated();
        unset($data['answers']);
        $interview = Interview::create($data);

        if( $request->filled('answers') ){
            $interview->interview_questions()->sync($request->answers);
        }

        return new InterviewResource($interview);
    }

    public function show(Interview $interview)
    {
        return new InterviewResource($interview);
    }

    public function update(UpdateInterviewRequest $request, Interview $interview)
    {
        $data = $request->validated();
        unset($data['answers']);
        $data['signed_at'] = !$interview->signed_at && $request->signed_at ? now() : null;

        $interview->update($data);

        if( $request->filled('answers') ){
            $interview->interview_questions()->sync($request->answers);
        }
        return new InterviewResource($interview);
    }

    public function destroy(Interview $interview)
    {
        $interview->delete();
        return response()->json(['data' => $interview], 204);
    }
}
