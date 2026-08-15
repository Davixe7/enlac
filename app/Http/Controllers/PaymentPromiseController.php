<?php

namespace App\Http\Controllers;

use App\Mail\DeductibleReceiptRequested;
use App\Models\PaymentPromise;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaymentPromiseController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'procuration_activity_id' => ['required', 'integer', 'exists:procuration_activities,id'],
            'donor_id'                => ['required', 'integer', 'exists:donors,id'],
            'payment_type'            => ['nullable', 'string', 'max:255'],
            'amount'                  => ['required', 'numeric', 'gt:0', 'max:99999999.99'],
            'date'                    => ['required', 'date'],
            'radiomarathon_key_id'    => ['required', 'integer', 'exists:radiomarathon_keys,id'],
            'anonymous'               => ['sometimes', 'boolean'],
            'deductible_receipt'      => ['sometimes', 'boolean'],
        ]);

        $paymentPromise = PaymentPromise::create($data);

        if (!empty($paymentPromise->deductible_receipt)) {
            $treasuryUsers = User::role('tesoreria')->whereNotNull('email')->get();

            if ($treasuryUsers->isNotEmpty()) {
                Mail::to($treasuryUsers)->send(new DeductibleReceiptRequested($paymentPromise));
            }
        }

        return response()->json([
            'data' => $paymentPromise
        ]);
    }
}
