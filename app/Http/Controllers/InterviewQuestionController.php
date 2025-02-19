<?php

namespace App\Http\Controllers;

use App\Http\Resources\InterviewQuestionResource;
use App\Models\InterviewQuestion;
use Illuminate\Http\Request;

class InterviewQuestionController extends Controller
{
    public function index()
    {
        $interviewQuestions = InterviewQuestion::all();
        return InterviewQuestionResource::collection($interviewQuestions);
    }
}
