<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\BrainFunctionController;
use App\Http\Controllers\BrainFunctionRankController;
use App\Http\Controllers\BrainLevelController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CandidateKardexController;
use App\Http\Controllers\CandidateLocationController;
use App\Http\Controllers\CandidateStatusController;
use App\Http\Controllers\CandidateStatusUpdateController;
use App\Http\Controllers\DashboardSlideController;
use App\Http\Controllers\EquineRidesController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\InterviewQuestionController;
use App\Http\Controllers\KardexController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MedicationLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentConfigController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PersonalProgramController;
use App\Http\Controllers\PlanCategoryController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\RideController;
use App\Http\Controllers\WorkAreaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\UserController;
use App\Http\Resources\UserResource;
use App\Http\Resources\EvaluationFields;
use App\Http\Resources\EvaluatorResource;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\ActivityDailyScoreController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DailyAttendanceController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\ScoreReportController;

use App\Http\Controllers\reports\AttendanceReportController;
use App\Http\Controllers\reports\BeneficiaryAttendanceReportController;
use App\Http\Controllers\reports\BeneficiaryIndividualReportController;
use App\Http\Controllers\reports\BeneficiaryScoreReportController;
use App\Http\Controllers\reports\ExcecutiveReportController;
use App\Http\Controllers\reports\GeneralReportController;
use App\Models\CandidateStatusLog;

Route::get('financial', [FinancialController::class, 'index']);
Route::get('financial/semaforo', [FinancialController::class, 'semaforo']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn () => new UserResource(auth()->user()))->name('user');
    Route::get('test', [FinancialController::class, 'semaforo']);

    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);

    Route::get('candidates/dashboard', [CandidateController::class, 'dashboard']);
    Route::post('candidates/{candidate}/kardexes', [CandidateKardexController::class, 'store']);
    Route::get('candidates/{candidate}/kardexes', [CandidateKardexController::class, 'show']);
    Route::delete('candidates/{candidate}/kardexes', [CandidateKardexController::class, 'destroy']);

    Route::apiResources([
        'candidates'            => CandidateController::class,
        'candidate_locations'   => CandidateLocationController::class,
        'medications'           => MedicationController::class,
        'contacts'              => ContactController::class,
        'addresses'             => AddressController::class,
        'programs'              => ProgramController::class,
        'appointments'          => AppointmentController::class,
        'interviews'            => InterviewController::class,
        'brain_levels'          => BrainLevelController::class,
        'brain_functions'       => BrainFunctionController::class,
        'brain_function_ranks'  => BrainFunctionRankController::class,
        'interview_questions'   => InterviewQuestionController::class,
        'work_areas'            => WorkAreaController::class,
        'roles'                 => RoleController::class,
        'users'                 => UserController::class,
        'sponsors'              => SponsorController::class,
        'payment_configs'       => PaymentConfigController::class,
        'kardexes'              => KardexController::class,
        'dashboard-slides'      => DashboardSlideController::class,
        'payments'              => PaymentController::class,
        'groups'                => GroupController::class,
        'plans'                 => PlanController::class,
        'rides'                 => RideController::class,
        'equinetherapy_rides'   => EquineRidesController::class,
        'activities'            => ActivityController::class,
        'plan_categories'       => PlanCategoryController::class,
        'candidate_statuses'    => CandidateStatusController::class,
        'issues'                => IssueController::class,
    ]);

    Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('attendances', [AttendanceController::class, 'store'])->name('attendances.store');
    Route::put('attendances/{attendance}', [AttendanceController::class, 'update'])->name('attendances.update');

    Route::get('attendances/candidates', [AttendanceController::class, 'candidates'])->name('attendances.candidates');
    Route::get('payments/{candidate}/export', [PaymentController::class, 'export']);

    Route::put('candidatestatuses/{candidate}', [CandidateStatusUpdateController::class, 'update']);

    Route::get('personal', function(Request $request){
        $request->validate(['area'=>'required']);
        $users = User::whereWorkAreaId($request->area)->get();
        return response()->json(['data' => $users]);
    });

    Route::get('evaluators', fn () => EvaluatorResource::collection( User::role('evaluator')->orderBy('name')->get() ));

    Route::post('contacts/validate', [ContactController::class, 'validate']);

    Route::get('evaluation_fields', function(Request $request){
        $data = ['candidate_id' => $request->candidate_id, 'signed_at' => null];
        $evaluationA = Evaluation::firstOrCreate($data, $data);
        $evaluationB = Evaluation::whereCandidateId($request->candidate_id)->whereNotNull('signed_at')->latest()->first();
        return response()->json(['data' => [
            'a' => new EvaluationFields($evaluationA),
            'b' => $evaluationB ? new EvaluationFields($evaluationB) : null
        ]]);
    });

    Route::post('dashboard-slides/reorder', [DashboardSlideController::class, 'reorder']);

    Route::put('candidates/{candidate}/admission', [CandidateController::class, 'admission']);
    Route::put('candidates/{candidate}/review', [CandidateController::class, 'review']);
    Route::get('beneficiaries', [BeneficiaryController::class, 'index']);
    Route::get('beneficiaries/reports', [BeneficiaryController::class, 'reports']);
    Route::get('beneficiaries/{candidate}', [BeneficiaryController::class, 'show']);
    Route::put('beneficiaries/{candidate}/equinetherapy', [BeneficiaryController::class, 'updateEquineTherapyPermissions']);

    Route::get('medication_logs/{candidate}', [MedicationLogController::class, 'index']);
    Route::post('medication_logs/{medication}', [MedicationLogController::class, 'store']);

    Route::apiResource('personal_programs', PersonalProgramController::class, ['parameters' => ['personal_programs' => 'plan']]);
    Route::post('personal_programs/{plan}/copy', [PersonalProgramController::class, 'copy']);

    // Status history y reingreso
    Route::post('beneficiaries/{candidate}/status', [BeneficiaryController::class, 'changeStatus']);
    Route::post('beneficiaries/{candidate}/reingreso', [BeneficiaryController::class, 'reingreso']);
    Route::delete('media/{media}', [MediaController::class, 'destroy']);

    Route::apiResource('activity_daily_scores', ActivityDailyScoreController::class)->only('index', 'store');

    Route::get('beneficiaries/{candidate}/reports/daily', [BeneficiaryScoreReportController::class, 'daily']);
    Route::get('beneficiaries/{candidate}/reports/monthly', [BeneficiaryScoreReportController::class, 'monthly']);
    Route::get('beneficiaries/{candidate}/individual', [BeneficiaryIndividualReportController::class, 'index']);
    Route::get('beneficiaries/{candidate}/scores', [BeneficiaryIndividualReportController::class, 'scores']);

    Route::get('reports/attendances', [AttendanceReportController::class, 'index']);
    Route::get('reports/general', [GeneralReportController::class, 'index']);
    Route::get('reports/excecutive', [ExcecutiveReportController::class, 'index']);
    Route::get('reports/rides', [RideController::class, 'index']);

    // Export routes for reports
    Route::get('reports/general/export', [GeneralReportController::class, 'export']);
    Route::get('reports/attendances/export', [AttendanceReportController::class, 'export']);
    Route::get('reports/excecutive/export', [ExcecutiveReportController::class, 'export']);
    Route::get('reports/attendances/daily/export', [BeneficiaryAttendanceReportController::class, 'export']);
    Route::get('beneficiaries/{candidate}/individual/export', [BeneficiaryIndividualReportController::class, 'export']);

    Route::get('reports/scores', [ScoreReportController::class, 'index']);
    Route::get('reports/attendances/daily', [BeneficiaryAttendanceReportController::class, 'daily']);

    Route::get('beneficiaries/{candidate}/reports/export', [BeneficiaryScoreReportController::class, 'export']);
    Route::get('beneficiaries/{candidate}/reports/exportMonthly', [BeneficiaryScoreReportController::class, 'exportMonthly']);

    Route::post('attendances/daily', [DailyAttendanceController::class, 'store']);

    Route::get('candidate_status_logs', function(Request $request){
        $start = $request->start_date;
        $end   = $request->end_date;
        $candidateId = $request->candidate_id;

        $data = CandidateStatusLog::with([
            'candidate' => fn($q)=>$q->fullName(),
        ])
        ->with('author')
        ->byBeneficiary($candidateId)
        ->whereBetween('created_at', [$start, $end])
        ->get();

        return response()->json(compact('data'));
    });
});
