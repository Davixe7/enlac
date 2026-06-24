<?php

namespace App\Console\Commands;

use App\Models\Candidate;
use App\Models\CandidateStatusLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ActivateProgrammedCandidates extends Command
{
    /**
     * El nombre y la firma del comando en consola.
     *
     * @var string
     */
    protected $signature = 'candidates:activate-programmed';

    /**
     * La descripción del comando.
     *
     * @var string
     */
    protected $description = 'Cambia el estatus a activo de los candidatos programados para hoy y registra el historial';

    /**
     * Ejecuta el comando.
     */

    public function handle()
    {
        $hoy = Carbon::today()->toDateString();

        // 1. Obtener los candidatos que cumplen la condición
        $candidates = Candidate::where('status', 'programado')
            ->whereDate('entry_date', $hoy)
            ->get();

        if ($candidates->isEmpty()) {
            $this->info('No se encontraron candidatos para activar el día de hoy.');
            return Command::SUCCESS;
        }

        $afectados = 0;

        // 2. Usamos una transacción de BD para asegurar que si falla el log, no se quede a medias
        DB::transaction(function () use ($candidates, &$afectados) {
            foreach ($candidates as $candidate) {
                // Actualizar el estado del candidato
                $candidate->status = 'activo';
                $candidate->save();

                // Crear el registro en la tabla de historial (Log)
                CandidateStatusLog::create([
                    'candidate_id' => $candidate->id,
                    'user_id'      => null, // Al ser un proceso automático (cron), queda como null o un ID de sistema
                    'status'       => 'activo',
                    'comments'     => 'Activación automática por el sistema (Cronjob de fecha de ingreso).',
                ]);

                $afectados++;
            }
        });

        $this->info("Proceso completado con éxito. Se activaron {$afectados} candidatos y se generaron sus logs.");
        return Command::SUCCESS;
    }
}
