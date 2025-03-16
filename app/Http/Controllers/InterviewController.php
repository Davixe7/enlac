<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Http\Resources\InterviewResource;
use App\Http\Requests\StoreInterviewRequest;
use App\Http\Requests\UpdateInterviewRequest;
use App\Services\InterviewService;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    protected $interviewService;

    public function __construct(InterviewService $interviewService)
    {
        $this->interviewService = $interviewService;
    }

    public function index()
    {
        if( request()->filled('candidate_id') ){
            $interview = Interview::whereCandidateId(request()->candidate_id)->firstOrFail();
            return new InterviewResource($interview);
        }
        $interviews = Interview::all();
        return InterviewResource::collection($interviews);
    }

    public function store(Request $request)
    {
        $interview = $this->interviewService->createInterview($request);
        return new InterviewResource($interview);
    }

    public function show(Interview $interview)
    {
        return new InterviewResource($interview);
    }

    public function update(UpdateInterviewRequest $request, Interview $interview)
    {
        $this->interviewService->updateInterview($request, $interview);
        return new InterviewResource($interview);
    }

    public function destroy(Interview $interview)
    {
        $interview->delete();
        return response()->json(['data' => $interview], 204);
    }
}
