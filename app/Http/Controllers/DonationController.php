<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonationRequest;
use App\Models\Donation;
use App\Services\DonationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
     public function __construct(
        protected DonationService $donationService
    ) {}

    public function index(Request $request) {
        $query = Donation::query();

        if( $request->has('raffle_ticket_id') ){
            $query->where('raffle_ticket_id', $request->raffle_ticket_id);
        }

        $data = $query->get();
        return response()->json(compact('data'));
    }

    private function createDonationWithFolio($data)
    {
        return DB::transaction(function () use ($data) {
            $yearIndicator = '26';
            $prefix = "P-{$yearIndicator}-";

            $lastDonation = Donation::where('folio_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $nextNumber = $lastDonation ? (int) substr($lastDonation->folio_number, -5) + 1 : 1;
            $data['folio_number'] = $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            return Donation::create($data);
        });
    }

    public function store(StoreDonationRequest $request): JsonResponse
    {
        $donation = $this->donationService->createWithFolio($request->validated());

        return response()->json(['message' => 'Donativo aplicado con éxito', 'data' => $donation], 201);
    }

    public function storeAndPrint(Request $request)
    {
        $data = $request->except(['raffle_ticket_id']);

        if (isset($data['fiscal_record_id']) && !is_numeric($data['fiscal_record_id'])) {
            $data['fiscal_record_id'] = null;
        }

        $donation = $this->donationService->createWithFolio($data);
        $donation->load(['donor', 'fiscalRecord', 'procurationActivity', 'sponsor']);

        $pdf = Pdf::loadView('pdf.donation_receipt', compact('donation'))
            ->setPaper([0, 0, 226.77, 368.5], 'portrait');

        return $pdf->download('recibo_' . $donation->folio_number . '.pdf');
    }

    public function storeAndPrintRadiomarathon(Request $request)
    {
        $data = $request->except(['full_name', 'raffle_ticket_id']);

        if ($request->input('source') === 'others') {
            unset($data['donor_id']);
        }

        $data['activity_type'] = 'radiomarathon';

        $donation = $this->donationService->createWithFolio($data);
        $donation->load(['donor', 'sponsor', 'radiomarathonKey']);

        $pdf = Pdf::loadView('pdf.radiomarathon_receipt', compact('donation'))
                ->setPaper([0, 0, 226.77, 368.5], 'portrait');

        return $pdf->download('recibo_radiomarathon_' . $donation->folio_number . '.pdf');
    }

    public function getLinesByDonor($donorId): JsonResponse
    {
        $donations = Donation::where('donor_id', $donorId)
            ->orderBy('payment_date', 'desc')
            ->get();

        return response()->json(['data' => $donations], 200);
    }

    public function cancel(Donation $donation): JsonResponse
    {
        $donation->update([
            'cancelled_at' => now(),
            'cancelled_by_user_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Donativo cancelado correctamente.',
            'data'    => $donation
        ]);
    }
}
