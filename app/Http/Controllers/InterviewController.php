<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Http\Resources\InterviewResource;
use App\Http\Requests\StoreInterviewRequest;
use App\Http\Requests\UpdateInterviewRequest;
use App\Models\InterviewInterviewQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $data['signed_at'] = $request->sign ? now() : null;

        $interview = Interview::create($data);

        if( $request->filled('answers') ){
            $answers = collect( $request->answers )
                        ->map(fn($answer)=>collect($answer)->only(['interview_question_id', 'content']))
                        ->keyBy('interview_question_id');

            $interview->answers()->syncWithoutDetaching($answers->toArray());
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
        $data['signed_at'] = !$interview->signed_at && $request->sign ? now() : null;

        $interview->update($data);

        if( $request->filled('answers') ){
            $answers = collect( $request->answers )
                        ->map(fn($answer)=>collect($answer)->only(['interview_question_id', 'content']))
                        ->keyBy('interview_question_id');

            $interview->answers()->syncWithoutDetaching($answers->toArray());
        }
        return new InterviewResource($interview);
    }

    public function destroy(Interview $interview)
    {
        $interview->delete();
        return response()->json(['data' => $interview], 204);
    }
}
