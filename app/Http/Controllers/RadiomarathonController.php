<?php
namespace App\Http\Controllers;

use App\Models\ProcurationActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RadiomarathonController extends Controller
{
    public function allDonations(Request $request, ProcurationActivity $procurationActivity): JsonResponse
    {
        $activityId = $procurationActivity->id;

        $prospects = $this->getProspectPromisesQuery($activityId);
        $generalDonations = $this->getGeneralDonationsQuery($activityId);

        $combined = $prospects->unionAll($generalDonations)->get();

        $results = $combined->map(fn ($row) => $this->formatDonationRow($row));

        return response()->json(['data' => $results]);
    }

    private function getProspectPromisesQuery(int $activityId)
    {
        return DB::table('payment_promises as pp')
            ->join('donors as d', 'pp.donor_id', '=', 'd.id')
            ->leftJoin('donations as don', function ($join) use ($activityId) {
                $join->on('don.donor_id', '=', 'd.id')
                    ->where('don.procuration_activity_id', '=', $activityId);
            })
            ->where('pp.procuration_activity_id', $activityId)
            ->select(
                'd.id as donor_id',
                DB::raw("NULL as donation_id"),
                DB::raw("'prospecto' as source"),
                'pp.amount as promised_amount',
                DB::raw("TRIM(CONCAT_WS(' ', d.first_name, d.last_name, d.second_last_name)) as donor_name"),
                'd.company_name',
                DB::raw('COALESCE(SUM(don.amount), 0) as total_donated'),
                DB::raw('NULL as folio_number'),
                DB::raw('MAX(don.payment_date) as payment_date'),
                DB::raw('NULL as payment_method')
            )
            ->groupBy('d.id', 'pp.amount', 'd.first_name', 'd.last_name', 'd.second_last_name', 'd.company_name');
    }

    private function getGeneralDonationsQuery(int $activityId)
    {
        return DB::table('donations as don')
            ->where('don.procuration_activity_id', $activityId)
            ->whereNull('don.donor_id')
            ->select(
                DB::raw('NULL as donor_id'),
                'don.id as donation_id',
                'don.source',
                DB::raw('NULL as promised_amount'),
                'don.donor_name',
                DB::raw('NULL as company_name'),
                'don.amount as total_donated',
                'don.folio_number',
                'don.payment_date',
                'don.payment_method'
            );
    }

    private function formatDonationRow($row): array
    {
        $promised = (float) $row->promised_amount;
        $donated = (float) $row->total_donated;
        $donorName = $row->donor_name;
        $companyName = $row->company_name;

        if ($row->source === 'boteo') {
            $donorName = 'N/A';
            $companyName = 'N/A';
        } else {
            $donorName = $donorName ?? 'Sin registrar';
            if (!$companyName && preg_match('/^(.*?)\s*\((.*?)\)$/', $donorName, $matches)) {
                $donorName = trim($matches[1]);
                $companyName = trim($matches[2]);
            }
        }

        // Lógica de Estatus Coherente
        if ($row->source === 'prospecto') {
            if ($donated <= 0) {
                $status = 'pendiente';
            } elseif ($promised > 0 && $donated >= $promised) {
                $status = 'total';
            } else {
                $status = 'parcial';
            }
        } else {
            // Donativos directos/generales no tienen promesa
            $status = 'recibido';
        }

        $formattedDate = null;
        if ($row->payment_date) {
            try {
                $formattedDate = Carbon::parse($row->payment_date)->format('d/m/Y');
            } catch (\Exception $e) {
                $formattedDate = $row->payment_date;
            }
        }

        return [
            'id' => $row->donation_id,
            'donor_id' => $row->donor_id,
            'source' => $row->source,
            'donor_name' => $donorName,
            'company_name' => $companyName ?? 'N/A',
            'promised_amount' => $row->promised_amount ? number_format($promised, 2, '.', '') : null,
            'total_donated' => number_format($donated, 2, '.', ''),
            'status' => $status,
            'folio_number' => $row->folio_number ?? 'N/A',
            'payment_date' => $formattedDate,
            'raw_payment_date' => $row->payment_date, // Para filtrado exacto ISO
            'payment_method' => $row->payment_method ?? 'N/A',
            'amount' => number_format($donated, 2, '.', '')
        ];
    }
}
