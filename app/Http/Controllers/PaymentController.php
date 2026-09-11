<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentsResource;
use App\Models\Candidate;
use App\Models\Payment;
use App\Models\PaymentConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $payments = Payment::whereCandidateId($request->candidate_id)
            ->with([
                'candidate' => fn($query) => $query->select(['id', 'first_name', 'last_name']),
                'user'      => fn($query) => $query->select(['id', 'name', 'last_name']),
            ])->get();
        return PaymentsResource::collection($payments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['payment_config_id' => 'required|exists:payment_configs,id']);

        $data = $request->validate([
            'payment_config_id' => ['required', 'exists:payment_configs,id'],
            'candidate_id'      => ['required', 'exists:candidates,id'],
            'sponsor_id'        => ['nullable', 'exists:sponsors,id'],
            'payment_type'      => ['required', 'in:parent,sponsor'],
            'is_partial'        => ['required', 'boolean'],
            'date'              => ['required', 'date'],
            'payment_method'    => ['required', 'string'],
            'ref'               => ['nullable', 'string'],
            'comments'          => ['nullable', 'string'],
            'amount'            => ['required', 'numeric', 'min:0'],
        ]);

        try {
            return DB::transaction(function() use ($request, $data) {
                $data['created_by_id'] = auth()->id();

                // 1. Obtener la configuración primero
                $paymentConfig = PaymentConfig::with('sponsorship')->find($request->payment_config_id);

                // 2. Si la configuración tiene sponsor_id o pertenece a un padrinazgo, forzar el tipo y el sponsor_id
                if ($paymentConfig && $paymentConfig->sponsor_id) {
                    $data['sponsor_id']   = $paymentConfig->sponsor_id;
                    $data['payment_type'] = 'sponsor';
                } elseif ($paymentConfig && $paymentConfig->sponsorship && $paymentConfig->sponsorship->type === 'sponsor') {
                    $data['sponsor_id']   = $paymentConfig->sponsorship->sponsor_id;
                    $data['payment_type'] = 'sponsor';
                }

                // 3. Crear el pago con los datos correctos
                $payment = Payment::create($data);

                foreach($request->targetMonths as $targetMonth){
                    $amount = $request->is_partial ? $request->amount : $targetMonth['goal_amount'];
                    $payment->paymentDetails()->create([
                        'payment_config_id' => $paymentConfig->id,
                        'candidate_id'      => $payment->candidate_id,
                        'amount'            => $amount,
                        'year'              => $targetMonth['year'],
                        'month'             => $targetMonth['month'],
                    ]);
                }

                return response()->json(['data' => $payment]);
            });
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function export(Candidate $candidate, Request $request)
    {
        $data = Payment::whereCandidateId($candidate->id)
        ->with([
            'candidate' => fn($query) => $query->select(['id', 'first_name', 'last_name']),
            'user'      => fn($query) => $query->select(['id', 'name', 'last_name', 'second_last_name'])
        ])
        ->get();

        $payments = $data->map(function ($payment) {
            return [
                'Fecha'          => $payment->date,
                'Registrado por' => $payment->user->full_name,
                'Concepto'       => $payment->payment_type == 'parent' ? 'Cuota de padres' : 'Cuota de padrinos',
                'Monto'          => $payment->amount,
                'Cobertura'      => $payment->is_partial ? 'Parcial' : 'Total',
                'Referencia'     => $payment->ref,
                'Comentarios'    => $payment->comments,
            ];
        });

        $filename = 'historial_pagos_' . $candidate->full_name . '_' . time() . '.xlsx';

        return (new FastExcel($payments))
        ->download($filename);
    }

    /**
     * Genera el recibo en PDF para un pago específico.
     */
    public function printReceipt(Payment $payment)
    {
        $payment->load([
            'candidate.legalGuardian',
            'candidate.enlacResponsible',
            'sponsor',
            'paymentConfig.sponsor',
            'user',
            'paymentDetails'
        ]);

        $monthNames = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $monthsFormatted = $payment->paymentDetails->map(function ($detail) use ($monthNames) {
            $monthName = $monthNames[$detail->month] ?? $detail->month;
            return "{$monthName} {$detail->year}";
        })->implode(', ');

        if ($payment->payment_type === 'sponsor' || $payment->sponsor_id || $payment->paymentConfig?->sponsor_id) {
            $sponsor = $payment->sponsor ?: $payment->paymentConfig?->sponsor;
            $payerName = ($sponsor && $sponsor->full_name && $sponsor->full_name !== 'Cuota de Padres')
                ? $sponsor->full_name
                : 'Padrino Institucional';
        } else {
            $guardian = $payment->candidate?->legalGuardian;

            if ($guardian && $guardian->full_name && $guardian->full_name !== 'SIN DEFINIR') {
                $payerName = $guardian->full_name;
            } else {
                $responsible = $payment->candidate?->enlacResponsible;
                if ($responsible && $responsible->full_name && $responsible->full_name !== 'N/A') {
                    $payerName = $responsible->full_name;
                } else {
                    $payerName = 'Padre / Tutor';
                }
            }
        }

        $data = [
            'folio'          => $payment->folio,
            'date'           => $payment->date ? date('d/m/Y', strtotime($payment->date)) : $payment->created_at->format('d/m/Y'),
            'concept'        => $payment->payment_type === 'parent' ? 'Cuota de Padres' : 'Aportación de Padrinos',
            'payer_name'     => $payerName,
            'beneficiary'    => $payment->candidate ? $payment->candidate->full_name : 'N/A',
            'period'         => $monthsFormatted ?: 'N/A',
            'payment_method' => $payment->payment_method,
            'ref'            => $payment->ref,
            'amount'         => $payment->amount,
            'user_name'      => $payment->user ? $payment->user->full_name : 'Sistema',
        ];

        $pdf = Pdf::loadView('pdf.payment_receipt', $data)
            ->setPaper([0, 0, 226.77, 368.50]);

        return $pdf->stream("recibo_pago_{$payment->id}.pdf");
    }
}
