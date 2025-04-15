<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentConfigRequest;
use App\Http\Requests\UpdatePaymentConfigRequest;
use App\Http\Resources\PaymentConfigResource;
use App\Models\PaymentConfig;
use Illuminate\Http\Request;

class PaymentConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $paymentConfigs = PaymentConfig::bySponsor( $request->sponsor_id )->with(['candidate'])->get();
        return PaymentConfigResource::collection( $paymentConfigs );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentConfigRequest $request)
    {
        $data = $request->validated();
        $paymentConfig = PaymentConfig::create($data);
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
        $paymentConfig->update($data);
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
