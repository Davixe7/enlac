<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
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
            'candidate_id' => ['required', 'exists:candidates,id'],
            'sponsor_id' => ['nullable', 'exists:sponsors,id'],
            'payment_type' => ['required', 'in:parent,sponsor'],
            'is_partial' => ['required', 'boolean'],
            'date' => ['required', 'date'],
            'payment_method' => ['required', 'string'],
            'ref' => ['nullable', 'string'],
            'comments' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $payment = Payment::create($data);
        return response()->json(['data'=>$payment]);
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
}
