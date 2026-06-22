<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSponsorshipRequest;
use App\Http\Requests\UpdateSponsorshipRequest;
use App\Http\Resources\SponsorshipResource;
use App\Models\DeductibleReceipt;
use App\Models\PaymentConfigLog;
use App\Models\Sponsorship;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SponsorshipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sponsorships = Sponsorship::with(['candidate', 'sponsor'])
        ->bySponsor( $request->sponsor_id )
        ->byCandidate( $request->candidate_id )
        ->get();

        if( $request->filled(['candidate_id', 'sponsor_id']) ){
            return new SponsorshipResource( $sponsorships->first() );
        }

        return SponsorshipResource::collection( $sponsorships );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSponsorshipRequest $request)
    {
        $data = $request->validated();
        unset($data['receipt']);

        $sponsorship = Sponsorship::create($data);

        // Crea el primer snapshot cuando se genera un PaymentConfig
        $sponsorship->paymentConfigs()->create([
            'amount'          => $sponsorship->amount,
            'frequency'       => $sponsorship->frequency,
            'effective_since' => now()->toDateString(),
            'effective_until' => null,
            'candidate_id'    => $sponsorship->candidate_id,
            'sponsor_id'      => $sponsorship->sponsor_id ?: 0,
        ]);

        $receiptData = $request->validated()['receipt'] ?? null;
        if( $receiptData ){
            unset($receiptData['fiscalStatusFile']);
            $fiscalStatusFile = $request->file('receipt.fiscalStatusFile');
            $receipt          = $sponsorship->deductible_receipt()->create($receiptData);
            $receipt->addMedia($fiscalStatusFile)->toMediaCollection('fiscalStatus');
        }

        return new SponsorshipResource($sponsorship);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sponsorship $sponsorship)
    {
        return new SponsorshipResource( $sponsorship );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSponsorshipRequest $request, Sponsorship $sponsorship)
    {
        $data = $request->validated();
        unset($data['receipt']);

        $today             = now()->toDateString();
        $originalAmount    = $sponsorship->amount;
        $originalFrequency = $sponsorship->frequency;

        $sponsorship->update($data);
        $isDirty           = $originalAmount != $sponsorship->amount || $originalFrequency != $sponsorship->frequency;

        if ($isDirty) {
            Log::info('Is Dirty');
            $sponsorship->paymentConfig->update(['effective_until' => $today]);
            $sponsorship->paymentConfigs()->create([
                'amount'          => $sponsorship->amount,
                'frequency'       => $sponsorship->frequency,
                'candidate_id'    => $sponsorship->candidate_id,
                'effective_since' => $today,
                'effective_until' => null,
            ]);
        }

        $receiptData = $request->validated()['receipt'] ?? null;
        if( $receiptData ){
            unset($receiptData['fiscalStatusFile']);
            $fiscalStatusFile = $request->file('receipt.fiscalStatusFile');
            $receipt = DeductibleReceipt::updateOrCreate(['sponsorship_id' => $sponsorship->id], $receiptData);
            $receipt->addMedia($fiscalStatusFile)->toMediaCollection('fiscalStatus');
        }

        return new SponsorshipResource($sponsorship);
    }

    public function destroy(Sponsorship $sponsorship, Request $request) {
        $id = $sponsorship->id;

        PaymentConfigLog::create([
            'created_at'     => now(),
            'sponsorship_id' => $id,
            'action'         => 'cancelled',
            'reason'         => $request->cancellation_reason,
        ]);

        // Borrar (Soft Delete)
        $sponsorship->update(['cancellation_reason' => $request->cancellation_reason]);
        $sponsorship->delete();
    }

    public function restore($id) {
        $config = Sponsorship::onlyTrashed()->findOrFail($id);

        PaymentConfigLog::create([
            'sponsorship_id' => $id,
            'action' => 'restored',
            'created_at' => now()
        ]);

        // Restaurar
        $config->restore();
        $config->update(['cancellation_reason' => null]);
    }

    public function trashed(Request $request)
    {
        $query = Sponsorship::onlyTrashed()->with(['candidate', 'sponsor']);

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
        $configs = Sponsorship::onlyTrashed()
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
        $exists = Sponsorship::onlyTrashed()
            ->where('candidate_id', $request->candidate_id)
            ->exists();

        return response()->json(['has_history' => $exists]);
    }

    public function getHistoryLogs($id)
    {
        $logs = \App\Models\PaymentConfigLog::where('sponsorship_id', $id)
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
