<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Http\Requests\StoreDonationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
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
        $donation = $this->createDonationWithFolio($request->validated());
        return response()->json(['message' => 'Donativo aplicado con éxito', 'data' => $donation], 201);
    }

    public function storeAndPrint(Request $request)
    {
        $data = $request->all();

        if (isset($data['fiscal_record_id']) && !is_numeric($data['fiscal_record_id'])) {
            $data['fiscal_record_id'] = null;
        }

        $donation = $this->createDonationWithFolio($data);
        $donation->load(['donor', 'fiscalRecord', 'procurationActivity', 'sponsor']);

        $pdf = Pdf::loadView('pdf.donation_receipt', compact('donation'))
                ->setPaper([0, 0, 226.77, 368.5], 'portrait');

        return $pdf->download('recibo_' . $donation->folio_number . '.pdf');
    }

    public function getLinesByDonor($donorId): JsonResponse
    {
        // Buscamos los donativos ordenados del más reciente al más antiguo
        $donations = Donation::where('donor_id', $donorId)
            ->orderBy('payment_date', 'desc')
            ->get();

        return response()->json([
            'data' => $donations
        ], 200);
    }
}
