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
        if( $request->filled('wants_deductible_receipt') ){
            $data = $request->validated()['receipt'];
            $paymentConfig->deductible_receipt()->create($data);
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

        $paymentConfig->update($data);
        
        if( array_key_exists('receipt', $request->validated()) ){
            $data = $request->validated()['receipt'];
            DeductibleReceipt::updateOrCreate(['payment_config_id'=>$paymentConfig->id], $data);
        }

        return new PaymentConfigResource($paymentConfig);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentConfig $paymentConfig)
    {
        //
    }
}
