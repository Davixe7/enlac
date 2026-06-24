<?php

namespace App\Console\Commands;

use App\Models\ParentQuotaUpdate;
use App\Models\PaymentConfig;
use App\Models\Sponsorship;
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
            $configs = PaymentConfig::whereHas('sponsorship', function($query){
                $query->whereType('parent');
            })
            ->whereNull('effective_until')
            ->increment('amount', $increase->amount);

            $sponsorships = Sponsorship::whereType('parent')
            ->whereDoesntHave('paymentConfig')
            ->increment('amount', $increase->amount);

            $this->info($configs + $sponsorships . " " . 'patrocinios conseguidos');

            $increase->update(['applied' => true]);

            ParentQuotaUpdate::create([
                'amount'      => $increase->amount,
                'valid_since' => Carbon::parse($increase->valid_since)->addYear()->toDateString(),
                'applied'     => false
            ]);

            $this->info('Aplicado con éxito');
            return Command::SUCCESS;
        });
    }
}
