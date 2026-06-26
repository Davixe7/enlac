<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProgramPrice;
use App\Models\Program;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateProgramPrices extends Command
{
    /**
     * El nombre y firma del comando en consola.
     */
    protected $signature = 'programs:update-prices';

    /**
     * La descripción del comando.
     */
    protected $description = 'Activa los precios programados para el día de hoy y actualiza el fast-read de los programas.';

    /**
     * Ejecutar la lógica del comando.
     */
    public function handle()
    {
        $today     = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');

        $preciosProgramados = ProgramPrice::where('valid_since', $today)->get();

        if ($preciosProgramados->isEmpty()) {
            $this->info('No hay cambios de precios programados para hoy.');
            return Command::SUCCESS;
        }

        foreach ($preciosProgramados as $nuevoPrecio) {
            DB::transaction(function () use ($nuevoPrecio, $yesterday) {

                // A. Deprecar el precio que era vigente hasta ayer para este programa
                ProgramPrice::where('program_id', $nuevoPrecio->program_id)
                    ->where('id', '!=', $nuevoPrecio->id)
                    ->whereNull('valid_until') // El que estaba activo
                    ->update(['valid_until' => $yesterday]);

                // B. Actualizar el fast-read en la tabla principal de Programas
                Program::where('id', $nuevoPrecio->program_id)
                    ->update(['price' => $nuevoPrecio->price]);

                $nuevoPrecio->update(['applied'=>1]);
            });
        }

        $this->info("Se aplicaron 1 cambios de precio.");
        return Command::SUCCESS;
    }
}
