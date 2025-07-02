<?php

use App\Models\Candidate;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('beca', function(){
    return DB::table('payment_configs')
    ->where('candidate_id', 1)
    ->groupBy('candidate_id')
    ->selectRaw('candidate_id, SUM(amount / frequency) as monthly_amount')
    ->pluck('monthly_amount');
});

Route::get('seasons', function(){
    //App\Models\PaymentConfig::create(['candidate_id' => 1, 'sponsor_id' => null, 'frequency' => 1, 'amount' => 500, 'month_payday'=>1]);
    $curntDate = now();
    $startDate = Carbon::parse('2024-06-01');
    $startYear = $startDate->month <= 7 ? $startDate->year - 1 : $startDate->year;
    $endYear   = $curntDate->month >= 7 ? $curntDate->year + 1 : $curntDate->year;
    $seasons   = [];

    while($startYear < $endYear){
        $seasons[] = ["$startYear-".$startYear+1];
        $startYear++;
    }

    return $seasons;
});

Route::get('/test', function(Request $request){
    $candidate = Candidate::first();
    $paymentsConfigs = $candidate->payment_configs;
    $wallets = [];
    $year = now()->month > 7 ? now()->year : now()->year - 1;

    $paymentsConfigs->each(function($paymentConfig) use($candidate, &$wallets, $year) {
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
                //echo $paymentConfig->id . ' ' . $month . "<br/>";
                $abono = $carry >= $paymentConfig->monthly_amount ? $paymentConfig->monthly_amount : $carry;

                $date = Carbon::create($year, $month);
                $maxDate = Carbon::create($year, $end)->endOfMonth();
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

Route::get('migrate', function(){
    $exitCode = Artisan::call('migrate:fresh --seed');
    $output = Artisan::output();
    if ($exitCode === 0) {
        // El comando se ejecutó exitosamente
        echo "Comando ejecutado correctamente.\n";
        echo $output;
    } else {
        // Hubo un error al ejecutar el comando
        echo "Error al ejecutar el comando.\n";
        echo $output;
    }
});


// Ruta comodín para la SPA de Quasar (debe estar al final)
Route::get('/{any?}', function () {
    return view('spa');
})->where('any', '^(?!assets/|css/|js/|img/).*$');
