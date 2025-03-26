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

Route::get('/user', fn (Request $request) => $request->user())->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {

    Route::get('candidates/dashboard', [CandidateController::class, 'dashboard']);

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

    Route::put('candidates/{candidate}/admission', [CandidateController::class, 'admission']);

    Route::post('contacts/validate', [ContactController::class, 'validate']);

    Route::get('evaluation_fields', fn (Request $request) => new EvaluationFields($request));

    Route::get('evaluators', function () {
        return response()->json(['data' => User::role('evaluator')->get()]);
    });
});
