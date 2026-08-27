<?php

namespace App\Http\Controllers;

use App\Models\PaymentPromise;
use Illuminate\Http\Request;

class PaymentPromiseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'procuration_activity_id' => ['required', 'integer', 'exists:procuration_activities,id'],
            'donor_id'                => ['required', 'integer', 'exists:donors,id'],
            'payment_type'            => ['required', 'string', 'max:255'],
            'amount'                  => ['required', 'numeric', 'gt:0', 'max:99999999.99'],
            'date'                    => ['required', 'date'],
            'radiomarathon_key_id'    => ['required', 'integer', 'exists:radiomarathon_keys,id'],
            'anonymous'               => ['sometimes', 'boolean'],
            'deductible_receipt'      => ['sometimes', 'boolean'],
        ]);

        $data = PaymentPromise::create($data);
        return response()->json(compact('data'));
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentPromise $paymentPromise)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentPromise $paymentPromise)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentPromise $paymentPromise)
    {
        //
    }
}
