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

    $sponsors->each(function($sponsor) use($candidate) {
        $paymentConfig = $sponsor->payment_configs()->where('candidate_id', $candidate->id)->first();
        $payments = $sponsor->payments()
                    ->whereBetween('date', [now()->startOfYear(), now()->endOfYear()]);

        for ($i=1; $i < 13; $i += $paymentConfig->frequency) {
            $start = $i;
            $end = $i + $paymentConfig->frequency - 1;

            $startDate = Carbon::create(now()->year, $start);
            $endDate = Carbon::create(now()->year, $end)->endOfMonth();

            $payments = $sponsor->payments()
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('candidate_id', $candidate->id)
                    ->get();

            return $payments;
        }
    });
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
