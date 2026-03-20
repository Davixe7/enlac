<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentConfigRequest;
use App\Http\Requests\UpdatePaymentConfigRequest;
use App\Http\Resources\PaymentConfigResource;
use App\Models\DeductibleReceipt;
use App\Models\PaymentConfig;
use App\Models\PaymentConfigLog;
use Carbon\Carbon;
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
            unset($receiptData['fiscalStatusFile']);
            $fiscalStatusFile = $request->file('receipt.fiscalStatusFile');
            $receipt          = $paymentConfig->deductible_receipt()->create($receiptData);
            $receipt->addMedia($fiscalStatusFile)->toMediaCollection('fiscalStatus');
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

        $receiptData = $request->validated()['receipt'] ?? null;
        if( $receiptData ){
            unset($receiptData['fiscalStatusFile']);
            $fiscalStatusFile = $request->file('receipt.fiscalStatusFile');
            $receipt = DeductibleReceipt::updateOrCreate(['payment_config_id' => $paymentConfig->id], $receiptData);
            $receipt->addMedia($fiscalStatusFile)->toMediaCollection('fiscalStatus');
        }

        return new PaymentConfigResource($paymentConfig);
    }

    public function destroy($id, Request $request) {
        $config = PaymentConfig::findOrFail($id);

        PaymentConfigLog::create([
            'payment_config_id' => $id,
            'action' => 'cancelled',
            'reason' => $request->cancellation_reason,
            'created_at' => now()
        ]);

        // Borrar (Soft Delete)
        $config->update(['cancellation_reason' => $request->cancellation_reason]);
        $config->delete();
    }

    public function restore($id) {
        $config = PaymentConfig::onlyTrashed()->findOrFail($id);

        PaymentConfigLog::create([
            'payment_config_id' => $id,
            'action' => 'restored',
            'created_at' => now()
        ]);

        // Restaurar
        $config->restore();
        $config->update(['cancellation_reason' => null]);
    }

    public function trashed(Request $request)
    {
        $query = PaymentConfig::onlyTrashed()->with(['candidate', 'sponsor']);

        if ($request->has('candidate_id')) {
            $query->where('candidate_id', $request->candidate_id);
        }

        $trashed = $query->get()->map(function ($config) {
            return [
                'id' => $config->id,
                'sponsor' => $config->sponsor,
                'cancellation_reason' => $config->cancellation_reason,
                'deleted_at_formatted' => Carbon::parse($config->deleted_at)->format('d/m/Y h:ia')
            ];
        });

        return response()->json(['data' => $trashed]);
    }

    public function allHistory(Request $request)
    {
        $configs = PaymentConfig::onlyTrashed()
            ->where('candidate_id', $request->candidate_id)
            ->with(['sponsor'])->get();

        $data = $configs->map(function ($config) {
            return [
                'id' => $config->id,
                'sponsor' => $config->sponsor,
                'amount' => (float) $config->amount,
                'cancellation_reason' => $config->cancellation_reason,
                'is_active' => false,
                'deleted_at_formatted' => $config->deleted_at
                    ? \Carbon\Carbon::parse($config->deleted_at)->format('d/m/Y h:ia')
                    : 'Activo'
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function hasHistory(Request $request)
    {
        $exists = PaymentConfig::onlyTrashed()
            ->where('candidate_id', $request->candidate_id)
            ->exists();

        return response()->json(['has_history' => $exists]);
    }

    public function getHistoryLogs($id)
    {
        $logs = \App\Models\PaymentConfigLog::where('payment_config_id', $id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                $action = match ($log->action) {
                    'restored' => 'Restaurado el:',
                    'cancelled' => 'Cancelado el:',
                    default => ucfirst($log->action),
                };

                return [
                    'action' => $action,
                    'reason' => $log->reason ?? 'Sin motivo',
                    'date'   => \Carbon\Carbon::parse($log->created_at)->format('d/m/Y h:ia')
                ];
            });

        return response()->json(['data' => $logs]);
    }
}
