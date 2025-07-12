<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\BrainFunctionController;
use App\Http\Controllers\BrainFunctionRankController;
use App\Http\Controllers\BrainLevelController;
use App\Http\Controllers\CandidateKardexController;
use App\Http\Controllers\DashboardSlideController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\InterviewQuestionController;
use App\Http\Controllers\KardexController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentConfigController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WorkAreaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\UserController;
use App\Http\Resources\UserResource;
use App\Http\Resources\EvaluationFields;
use App\Models\Candidate;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Carbon\Carbon;

Route::get('financial', [FinancialController::class, 'index']);

Route::get('/test', function(Request $request){
    $candidate = Candidate::findOrFail($request->candidate_id);
    $paymentsConfigs = $candidate->payment_configs;
    $wallets = [];
    $year = now()->month > 7 ? now()->year : now()->year - 1;

    $paymentsConfigs->each(function($paymentConfig) use(&$wallets, $year) {
        $carry = 0;

        for ($i=8; $i < 20; $i += $paymentConfig->frequency) {
            $start = $i;
            $end = $i + $paymentConfig->frequency - 1;

            $startDate = Carbon::create($year, $start);
            $endDate   = Carbon::create($year, $end)->endOfMonth();

            $balance = Payment::where('candidate_id', $paymentConfig->candidate_id)
                    ->where('sponsor_id', $paymentConfig->sponsor_id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->groupBy('candidate_id')
                    ->sum('amount');

            $carry = $carry + $balance;

            foreach(range($start, $end) as $month){
                $abono = $carry >= $paymentConfig->monthly_amount ? $paymentConfig->monthly_amount : $carry;

                $date = Carbon::create($year, $month);
                $maxDate = Carbon::create($year, $end)->startOfMonth()->addDays(10);
                $status = null;

                if( $abono == $paymentConfig->monthly_amount ){
                    $status = 'green';
                }
                elseif ( now() > $maxDate ) {
                    $status = 'red';
                }
                else {
                    $status = 'yellow';
                }

                $wallets[$paymentConfig->sponsor_id][] = [
                    'date' => $date->format('Y-m-d'),
                    'month' => $month,
                    'monthName' => $date->format('F'),
                    'abono' => number_format($abono, 2, '.', ''),
                    'status' => $status
                ];
                $carry = $carry - $abono;
            }
        }
    });

    return $wallets;
});

Route::get('/user', fn () => new UserResource(auth()->user()))
->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {

    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::get('candidates/dashboard', [CandidateController::class, 'dashboard']);

    Route::post('candidates/{candidate}/kardexes', [CandidateKardexController::class, 'store']);
    Route::get('candidates/{candidate}/kardexes', [CandidateKardexController::class, 'show']);
    Route::delete('candidates/{candidate}/kardexes', [CandidateKardexController::class, 'destroy']);

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
        'interview_questions'   => InterviewQuestionController::class,
        'work_areas'   => WorkAreaController::class,
        'roles'   => RoleController::class,
        'users'   => UserController::class,
        'sponsors'   => SponsorController::class,
        'payment_configs'   => PaymentConfigController::class,
        'kardexes'   => KardexController::class,
        'dashboard-slides'   => DashboardSlideController::class,
        'payments'   => PaymentController::class,
    ]);

    Route::post('dashboard-slides/reorder', [DashboardSlideController::class, 'reorder']);

    Route::put('candidates/{candidate}/admission', [CandidateController::class, 'admission']);

    Route::post('contacts/validate', [ContactController::class, 'validate']);

    Route::get('evaluation_fields', fn (Request $request) => new EvaluationFields($request));

    Route::get('evaluators', function (Request $request) {
        return response()->json(['data' => User::role('evaluator')->get()]);
    });

    Route::get('beneficiaries', [BeneficiaryController::class, 'index']);
    Route::get('beneficiaries/{candidate}', [BeneficiaryController::class, 'show']);
    Route::put('beneficiaries/{candidate}', [BeneficiaryController::class, 'update']);

    Route::get('personal', function(Request $request){
        $request->validate(['area'=>'required']);
        $users = User::whereWorkAreaId($request->area)->get();
        return response()->json(['data' => $users]);
    });
});
