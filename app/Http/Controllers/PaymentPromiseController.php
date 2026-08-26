<?php

namespace App\Http\Controllers;

use App\Mail\DeductibleReceiptRequested;
use App\Models\PaymentPromise;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

    public function update(Request $request, PaymentPromise $payment_promise){
        $validated = $request->validate([
            'anonymous'          => 'sometimes|boolean',
            'deductible_receipt' => 'sometimes|boolean',
            'published_at'       => 'sometimes',
        ]);

        // Guardar si se está intentando publicar
        $isPublishing = !empty($validated['published_at']);

        $validated['published_at'] = $isPublishing ? now() : null;

        $payment_promise->update($validated);

        // SOLO SI SE PUBLICÓ: Emitir evento al caché del Stream
        if ($isPublishing) {
            $payment_promise->load('donor');

            $donorName = 'Anónimo';
            $companyName = null;

            if (!$payment_promise->anonymous && $payment_promise->donor) {
                $donorName = trim("{$payment_promise->donor->first_name} {$payment_promise->donor->last_name}");
                $companyName = $payment_promise->donor->company_name;
            }

            $payload = [
                'donor_name'   => $donorName ?: 'Anónimo',
                'company_name' => $companyName,
                'amount'       => (float) $payment_promise->amount,
                'timestamp'    => microtime(true)
            ];

            Cache::put("radiomarathon_{$payment_promise->procuration_activity_id}_latest_stream", $payload, 3600);
        }

        $data = $payment_promise;

        return response()->json(compact('data'));
    }
}
