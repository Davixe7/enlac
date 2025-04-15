<?php

use App\Models\Candidate;
use App\Notifications\EvaluationScheduled;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

Route::get('test', function(){
    $candidate = Candidate::first();
    $sponsors = $candidate->sponsors;
    $wallets = [];

    $sponsors->each(function($sponsor) use($candidate, &$wallets) {
        $paymentConfig = $sponsor->payment_configs()->where('candidate_id', $candidate->id)->first();

        for ($i=1; $i < 13; $i += $paymentConfig->frequency) {
            $start = $i;
            $end = $i + $paymentConfig->frequency - 1;

            $startDate = Carbon::create(now()->year, $start);
            $endDate = Carbon::create(now()->year, $end)->endOfMonth();

            $payments = $sponsor->payments()
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('candidate_id', $candidate->id)
                    ->get();

            $balance = $payments->reduce(function($carry, $payment) use($paymentConfig) {
                return $payment->amount + $carry;
            },0);

            $carry = $balance;

            foreach(range($start, $end) as $month){
                //echo $paymentConfig->id . ' ' . $month . "<br/>";
                $abono = $carry >= $paymentConfig->monthly_amount ? $paymentConfig->monthly_amount : $carry;

                $nombreMes = (Carbon::create(now()->year, $month))->format('F');
                $status = null;

                if( $abono == $paymentConfig->monthly_amount ){
                    $status = 'green';
                }
                elseif ( now()->month > $end ) {
                    //echo now()->month . ' > ' . $end . "<br/>";
                    $status = 'red';
                }
                else {
                    $status = 'yellow';
                }

                $wallets[$sponsor->id][] = [
                    'month' => $nombreMes,
                    'abono' => $abono,
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
