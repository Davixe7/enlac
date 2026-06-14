<?php

namespace App\Console\Commands;

use App\Models\ParentQuotaUpdate;
use App\Models\PaymentConfig;
use App\Models\PaymentConfigSnapshot;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ApplyParentQuotaUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sponsorships:apply-increase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();

        // 1. Buscar incremento pendiente que ya deba aplicarse
        $increase = ParentQuotaUpdate::where('applied', false)
            ->where('valid_since', '<=', $today)
            ->first();

        if (!$increase) {
            $this->info('No hay incrementos pendientes por aplicar el día de hoy.');
            return Command::SUCCESS;
        }

        DB::transaction(function () use ($increase, $today) {
            // 2. Actualización masiva de todos los padrinos
            /* PaymentConfigSnapshot::where('effective_since', '<=', $today)
            ->whereNull('effective_until')
            ->increment('amount', $increase->amount); */

            $configs = PaymentConfig::whereType('parent')
            ->with('snapshot')
            ->get();

            $this->info($configs->count() . " " . 'Snapshots conseguidos');

            $configs->each(function($config)use($increase){
                $snap = $config->snapshot;
                $amount = $snap ? $snap->amount : $config->amount;
                $amount = $amount + $increase->amount;
                if( $snap ){
                    $snap->update(['amount' => $amount]);
                }
                $config->update(['amount' => $amount]);
            });

            // 3. Marcar el incremento actual como aplicado
            $increase->update(['applied' => true]);

            // 4. Crear el pendiente para el próximo año
            ParentQuotaUpdate::create([
                'amount' => $increase->amount,
                'valid_since' => Carbon::parse($increase->valid_since)->addYear()->toDateString(),
                'applied' => false
            ]);

            $this->info('Aplicado con éxito');
            return Command::SUCCESS;
        });
    }
}
