<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentsResource;
use App\Models\Candidate;
use App\Models\Payment;
use App\Models\PaymentConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

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
        $request->validate(['payment_config_id'=>'required|exists:payment_configs,id']);

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
            DB::transaction(function() use ($request, $data) {
                $data['created_by_id'] = auth()->id();
                $payment               = Payment::create($data);
                $paymentConfig         = PaymentConfig::find($request->payment_config_id);

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
            //'No se pudo crear el pago o alguno de sus detalles'
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
}
