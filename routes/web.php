<?php

use App\Models\Candidate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

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
