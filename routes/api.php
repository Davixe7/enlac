<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BrainFunctionController;
use App\Http\Controllers\BrainFunctionRankController;
use App\Http\Controllers\BrainLevelController;
use App\Http\Controllers\InterviewController;
use App\Http\Resources\EvaluationFields;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InterviewQuestionController;
use App\Http\Resources\CandidateResource;
use App\Models\Candidate;
use Illuminate\Support\Facades\DB;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResources([
        'candidates' => CandidateController::class,
        'medications' => MedicationController::class,
        'contacts' => ContactController::class,
        'addresses' => AddressController::class,
        'programs'  => ProgramController::class,
        'appointments'  => AppointmentController::class,
        'interviews'  => InterviewController::class,
        'brain_levels'  => BrainLevelController::class,
        'brain_functions'  => BrainFunctionController::class,
        'brain_function_ranks'  => BrainFunctionRankController::class,
        'interview_questions'   => InterviewQuestionController::class
    ]);

    Route::post('contacts/validate', [ContactController::class, 'validate']);

    Route::get('candidates/reports_by_name', function (Request $request){
        $candidates = Candidate::whereName($request->name)->get();
        return CandidateResource::collection($candidates);
    });

    Route::get('candidates/reports_by_birthdate', function (Request $request) {
        $candidates = Candidate::whereBirthDate($request->birthDate)->get();
        return CandidateResource::collection($candidates);
    });

    Route::get('candidates/reports_by_evaluation_date', function(Request $request){
        $candidatesWithRecentSchedule = Candidate::whereEvaluationBetween($request->startDate, $request->endDate)
        ->get()
        ->groupBy(function ($candidate) {
            if ($candidate->evaluation_status === 'done') {
                return $candidate->onboard_at ? 'done_onboarded' : 'done_not_onboarded';
            }
            return $candidate->evaluation_status;
        });

        $counts = $candidatesWithRecentSchedule->map(function ($group) {
            return $group->count();
        });

        $dataWithCount = $candidatesWithRecentSchedule->map(function ($group) {
            return CandidateResource::collection($group);
        });

        return response()->json([
            'counts' => $counts,
            'data' => $dataWithCount,
        ]);
    });

    Route::put('candidates/{candidate}/admission', [CandidateController::class, 'admission']);

    Route::get('evaluation_fields', function (Request $request) {
        return new EvaluationFields($request);
    });

    Route::get('interview_answers', function (Request $request) {
        $results = DB::table('interview_questions')
            ->leftJoin('interview_interview_question', function ($join) use ($request) {
                $join
                    ->on('interview_question_id', '=', 'interview_questions.id')
                    ->where('interview_interview_question.interview_id', $request->interview_id);
            })
            ->select(
                'interview_questions.question_text as question_text',
                'interview_questions.id as interview_question_id',
                'interview_questions.id as interview_question_id',
                'interview_interview_question.content as content',
            )
            ->get();

        $results = $results->map(function ($result) use ($request) {
            $result->interview_id = $request->interview_id;
            $result->content = strval($result->content);
            $result->checked = $result->content ? 1 : 0;
            return $result;
        });

        return response()->json(['data'=>$results]);
    });

    Route::get('evaluators', function () {
        return response()->json(['data' => User::role('evaluator')->get()]);
    });
});
