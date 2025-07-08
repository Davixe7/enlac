<?php

namespace App\Http\Controllers;

use App\Http\Resources\BeneficiaryFinancialResource;
use App\Models\Candidate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $date = Carbon::parse('2024-08-01');
        $start = $date->startOfMonth()->format('Y-m-d');
        $end   = $date->endOfMonth()->format('Y-m-d');

        //Sumariza todos los patrocinios por tipo en un monto total
        //Sumariza todos los pagos por tipo en un monto total y ultima fecha de pago
        $candidates = Candidate::whereAcceptanceStatus(1)
            ->with([
                'program',
                'payment_configs' => fn ($q) => $q->groupBy(['candidate_id', 'type'])->selectRaw('candidate_id, type, SUM(amount) as quota'),
                'payments' => function ($query) use ($start, $end) {
                    $query
                        ->whereBetween('date', [$start, $end])
                        ->groupBy(['candidate_id', 'payment_type'])
                        ->selectRaw('candidate_id, payment_type, MAX(date) as last_payment, SUM(amount) as total_paid');
                }
            ])
            ->get();

        $candidates = $candidates->map(function ($c) use ($date) {
            $c->parent_amount = $c->payment_configs->where('type', 'parent')->first()?->quota ?: 0;
            $c->sponsr_amount = $c->payment_configs->where('type', 'sponsor')->first()?->quota ?: 0;
            $c->enlacs_amount = $c->program->price - $c->sponsr_amount - $c->parent_amount;
            $c->last_parent_payment_date = $c->payments->where('payment_type', 'parent')->first()?->last_payment ?: null;
            $c->last_sponsr_payment_date = $c->payments->where('payment_type', 'sponsor')->first()?->last_payment ?: null;
            $c->parent_paid    = $c->payments->where('payment_type', 'parent')->first()?->total_paid ?: 0;
            $c->sponsr_paid    = $c->payments->where('payment_type', 'sponsor')->first()?->total_paid ?: 0;
            $c->parent_status  = $c->parent_paid == $c->parent_amount ? 'green-2' : ((now() <= $date->addDays(9)) ? 'yellow-2' : 'red-2');
            $c->sponsr_status  = $c->sponsr_paid == $c->sponsr_amount ? 'green-2' : 'red-2';
            return $c;
        });

        return BeneficiaryFinancialResource::collection($candidates);
    }
}
