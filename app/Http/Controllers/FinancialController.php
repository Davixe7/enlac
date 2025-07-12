<?php

namespace App\Http\Controllers;

use App\Http\Resources\BeneficiaryFinancialResource;
use App\Models\Candidate;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $month = request()->month ?: now()->month;
        $year  = request()->year ?: now()->year;
        $yearStart = Carbon::create( $year, 8 );
        $yearEnd   = Carbon::create( $year + 1, 8 );

        $date = compact('month', 'year', 'yearStart', 'yearEnd');

        //Sumariza todos los patrocinios por tipo en un monto total
        //Sumariza todos los pagos por tipo en un monto total y ultima fecha de pago
        $candidates = Candidate::whereAcceptanceStatus(1)
        ->with([
            'program',
            'payment_configs',
            'payment_confix' => fn ($q) => $q->groupBy(['candidate_id', 'type'])->selectRaw('candidate_id, type, SUM( ((12/frequency) * amount) / 12) as quota'),
        ])
        ->get();

        $candidates = $candidates->map(function($candidate)use($date){
            $candidate->sponsr_amount = $candidate->payment_confix->where('type', 'sponsor')->first()?->quota ?: 0;
            $candidate->parent_amount = $candidate->payment_confix->where('type', 'parent')->first()?->quota ?: 0;
            $candidate->enlacs_amount = $candidate->program->price - $candidate->sponsr_amount - $candidate->parent_amount;
            $candidate->sponsr_paid = 0;
            $candidate->parent_paid = 0;

            $candidate->payment_configs->where('type', 'parent')->map(function($paycfg) use (&$candidate, $date ) {
                $payments = Payment::whereBetween('date', [$date['yearStart'], $date['yearEnd']])
                ->where('candidate_id', $paycfg->candidate_id)
                ->where('sponsor_id', null)
                ->get();

                $candidate->last_parent_payment_date = $payments->max('date');

                $carry = $payments->sum('amount');

                for($i = 8; $i < 20; $i++){
                    $abono = $carry >= $paycfg->monthly_amount ? $paycfg->monthly_amount : $carry;
                    $carry = $carry - $abono;
                    if( $i == $date['month'] ){
                        $candidate->parent_paid = $candidate->parent_paid + $abono;
                    }
                }
            });

            $candidate->payment_configs->map(function($paycfg) use (&$candidate, $date ) {
                $payments = Payment::whereBetween('date', [$date['yearStart'], $date['yearEnd']])
                ->where('candidate_id', $paycfg->candidate_id)
                ->where('payment_type', 'sponsor')
                ->where('sponsor_id', $paycfg->sponsor_id)
                ->get();

                $candidate->last_sponsr_payment_date = $payments->max('date');
                $carry = $payments->sum('amount');

                for($i = 8; $i < 20; $i++){
                    $abono = $carry >= $paycfg->monthly_amount ? $paycfg->monthly_amount : $carry;
                    $carry = $carry - $abono;
                    if( $i == $date['month'] ){
                        $candidate->sponsr_paid = $candidate->sponsr_paid + $abono;
                    }
                }
            });

            return $candidate;
        });

        /* $candidates = $candidates->map(function ($c) use ($date) {
            $c->parent_amount = $c->payment_configs->where('type', 'parent')->first()?->quota ?: 0;
            $c->sponsr_amount = $c->payment_configs->where('type', 'sponsor')->first()?->quota ?: 0;
            $c->enlacs_amount = $c->program->price - $c->sponsr_amount - $c->parent_amount;
            $c->last_parent_payment_date = $c->payments->where('payment_type', 'parent')->first()?->last_payment ?: null;
            $c->last_sponsr_payment_date = $c->payments->where('payment_type', 'sponsor')->first()?->last_payment ?: null;
            $c->parent_paid    = $c->payments->where('payment_type', 'parent')->first()?->total_paid ?: 0;
            $c->sponsr_paid    = $c->payments->where('payment_type', 'sponsor')->first()?->total_paid ?: 0;
            $c->parent_status  = $c->parent_paid == $c->parent_amount ? 'green-2' : ((now() <= $date->addDays(9)) ? 'yellow-2' : 'red-2');
            $c->sponsr_status  = $c->sponsr_paid >= $c->sponsr_amount ? 'green-2' : 'red-2';
            return $c;
        }); */

        return BeneficiaryFinancialResource::collection($candidates);
    }
}
