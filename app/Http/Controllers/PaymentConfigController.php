<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentConfigRequest;
use App\Http\Requests\UpdatePaymentConfigRequest;
use App\Http\Resources\PaymentConfigResource;
use App\Models\DeductibleReceipt;
use App\Models\PaymentConfig;
use Illuminate\Http\Request;

class PaymentConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $paymentConfigs = PaymentConfig::with(['candidate', 'sponsor'])
        ->bySponsor( $request->sponsor_id )
        ->byCandidate( $request->candidate_id )
        ->get();

        if( $request->filled(['candidate_id', 'sponsor_id']) ){
            return new PaymentConfigResource( $paymentConfigs->first() );
        }

        return PaymentConfigResource::collection( $paymentConfigs );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentConfigRequest $request)
    {
        $data = $request->validated();
        unset($data['receipt']);

        $paymentConfig = PaymentConfig::create($data);

        // Crea el primer snapshot cuando se genera un PaymentConfig
        $paymentConfig->snapshots()->create([
            'amount'          => $paymentConfig->amount,
            'frequency'       => $paymentConfig->frequency,
            'effective_since' => now()->toDateString(),
            'effective_until' => null,
        ]);

        $receiptData = $request->validated()['receipt'] ?? null;
        if( $receiptData ){
            $paymentConfig->deductible_receipt()->create($receiptData);
        }
        
        return new PaymentConfigResource($paymentConfig);
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentConfig $paymentConfig)
    {
        return new PaymentConfigResource( $paymentConfig );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentConfigRequest $request, PaymentConfig $paymentConfig)
    {
        $data = $request->validated();
        unset($data['receipt']);

        $originalAmount = $paymentConfig->amount;
        $originalFrequency = $paymentConfig->frequency;

        $paymentConfig->update($data);

        if ($originalAmount != $paymentConfig->amount || $originalFrequency != $paymentConfig->frequency) {
            $today = now()->toDateString();

            $currentSnapshot = $paymentConfig->snapshots()
                ->whereNull('effective_until')
                ->orderByDesc('effective_since')
                ->first();

            if ($currentSnapshot) {
                $currentSnapshot->update([
                    'effective_until' => $today,
                ]);
            }

            $paymentConfig->snapshots()->create([
                'amount'          => $paymentConfig->amount,
                'frequency'       => $paymentConfig->frequency,
                'effective_since' => $today,
                'effective_until' => null,
            ]);
        }
        
        if( array_key_exists('receipt', $request->validated()) ){
            $data = $request->validated()['receipt'];
            DeductibleReceipt::updateOrCreate(['payment_config_id'=>$paymentConfig->id], $data);
        }

        return new PaymentConfigResource($paymentConfig);
    }

    public function destroy(PaymentConfig $paymentConfig)
    {
        if ($paymentConfig->type !== 'sponsor') {
            return response()->json([
                'error' => 'Solo se pueden desligar configuraciones de tipo sponsor'
            ], 400);
        }

        $paymentConfig->delete();

        return response()->json([
            'message' => 'Patrocinio cancelado correctamente. Recuerda hablar con los padres de familia para reponer la Aportación de Padrinos.'
        ]);
    }

}
