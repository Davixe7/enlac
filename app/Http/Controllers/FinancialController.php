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
        $yearStart = Carbon::create($year, 8);
        $yearEnd   = Carbon::create($year + 1, 8);

        $date = compact('month', 'year', 'yearStart', 'yearEnd');

        //Sumariza todos los patrocinios por tipo en un monto total
        //Sumariza todos los pagos por tipo en un monto total y ultima fecha de pago
        $candidates = Candidate::beneficiaries()
            ->with([
                'program',
                'payment_configs',
                'payment_confix' => fn($q) => $q->groupBy(['candidate_id', 'type'])->selectRaw('candidate_id, type, SUM( ((12/frequency) * amount) / 12) as quota'),
            ])
            ->get();

        $candidates = $candidates->map(function ($candidate) use ($date) {
            $candidate->sponsr_amount = $candidate->payment_confix->where('type', 'sponsor')->first()?->quota ?: 0;
            $candidate->parent_amount = $candidate->payment_confix->where('type', 'parent')->first()?->quota ?: 0;
            $candidate->enlacs_amount = $candidate->program->price - $candidate->sponsr_amount - $candidate->parent_amount;
            $candidate->sponsr_paid = 0;
            $candidate->parent_paid = 0;

            $candidate->payment_configs->where('type', 'parent')->map(function ($paycfg) use (&$candidate, $date) {
                $payments = Payment::whereBetween('date', [$date['yearStart'], $date['yearEnd']])
                    ->where('candidate_id', $paycfg->candidate_id)
                    ->where('sponsor_id', null)
                    ->get();

                $candidate->last_parent_payment_date = $payments->max('date');

                $carry = $payments->sum('amount');

                for ($i = 8; $i < 20; $i++) {
                    $abono = $carry >= $paycfg->monthly_amount ? $paycfg->monthly_amount : $carry;
                    $carry = $carry - $abono;
                    if ($i == $date['month']) {
                        $candidate->parent_paid = $candidate->parent_paid + $abono;
                    }
                }
            });

            $candidate->payment_configs->map(function ($paycfg) use (&$candidate, $date) {
                $payments = Payment::whereBetween('date', [$date['yearStart'], $date['yearEnd']])
                    ->where('candidate_id', $paycfg->candidate_id)
                    ->where('payment_type', 'sponsor')
                    ->where('sponsor_id', $paycfg->sponsor_id)
                    ->get();

                $candidate->last_sponsr_payment_date = $payments->max('date');
                $carry = $payments->sum('amount');

                for ($i = 8; $i < 20; $i++) {
                    $abono = $carry >= $paycfg->monthly_amount ? $paycfg->monthly_amount : $carry;
                    $carry = $carry - $abono;
                    if ($i == $date['month']) {
                        $candidate->sponsr_paid = $candidate->sponsr_paid + $abono;
                    }
                }
            });

            return $candidate;
        });

        return BeneficiaryFinancialResource::collection($candidates);
    }

    function maxDateByFreq($monthNumber, $frequency, $start){
        $periodLength  = max(1, (int) $frequency); // 1, 2, 3, 4, 6, 12...
        $offset        = $monthNumber - $start;              // 0 = agosto, 1 = septiembre, etc.
        $blockIndex    = intdiv($offset, $periodLength);
        $blockEndMonth = $start + ($blockIndex + 1) * $periodLength - 1; // último mes del bloque
        $blockEndMonth = min($blockEndMonth, 19);
        return $blockEndMonth;
    }

    public function semaforo(Request $request)
    {
        $candidate = Candidate::findOrFail($request->candidate_id);
        $paymentsConfigs = $candidate->payment_configs;
        $year = now()->month > 7 ? now()->year : now()->year - 1;
        $startDate = Carbon::create($year, 8);
        $endDate   = Carbon::create($year, 20)->endOfMonth();
        
        $wallets = [];

        $paymentsConfigs->each(function ($paymentConfig) use ($year, $startDate, $endDate, &$wallets) {
            $balance    = $paymentConfig->periodBalance($startDate, $endDate);
            $start      = 8;
            $lastSnapId = null;

            for ($monthNumber = 8; $monthNumber < 20; $monthNumber++) {
                $snapshot   = $paymentConfig->getSnapshotForPeriod($year, $monthNumber, 1) ?? $paymentConfig;
                $start      = $snapshot->id != $lastSnapId ? $monthNumber : $start;
                $lastSnapId = $snapshot->id;
                $maxDateMonth = $this->maxDateByFreq($monthNumber, $snapshot->frequency, $start);
                $steps        = $snapshot->frequency != .5 ? 1 : 2;

                $amountToPay = $snapshot->frequency == .5
                ? $snapshot->amount
                : $snapshot->monthly_amount;

                for($step = 1; $step <= $steps; $step++){
                    $this->applyToWallet($wallets, $balance, $amountToPay, $monthNumber, $maxDateMonth, $year, $paymentConfig->sponsor_id, $step);
                }
            }
        });

        return $wallets;
    }

    function applyToWallet(&$wallets, &$balance, $amountToPay, $monthNumber, $maxDateMonth, $year, $sponsor_id, $step){
        $abono = $balance >= $amountToPay
        ? $amountToPay
        : $balance;
    
        $maxDate = Carbon::create($year, $maxDateMonth);
        $maxDate = $step == 1
        ? $maxDate->copy()->addDays(10)
        : $maxDate->copy()->addDays(21);
    
        $status = $abono == $amountToPay ? 'green' : (( now() > $maxDate ) ? 'red' : 'yellow');
    
        $wallets[$sponsor_id][$monthNumber][] = [
            'month'     => $monthNumber,
            'monthName' => Carbon::create($year, $monthNumber)->translatedFormat('F'),
            'abono'     => $abono,
            'status'    => $status
        ];
    
        $balance -= $abono;
    }
}