<?php

use App\Notifications\EvaluationScheduled;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

Route::get('test', function(){
    $schedule = App\Models\EvaluationSchedule::first();
    return (new EvaluationScheduled($schedule))->toMail($schedule->evaluator);
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
