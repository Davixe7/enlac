<?php

namespace App\Http\Controllers;

use App\Exports\DonationExport;
use App\Models\Donation;
use App\Http\Requests\StoreDonationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ProcurationActivity;
use Illuminate\Support\Str;

class DonationController extends Controller
{

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
        $donation = $this->createDonationWithFolio($request->validated());
        $donation->payment_date = $donation->payment_date->format('d/m/Y');
        return response()->json(['message' => 'Donativo aplicado con éxito', 'data' => $donation], 201);
    }

    public function storeAndPrint(Request $request)
    {
        $data = $request->except(['raffle_ticket_id']);

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

    public function cancel(Donation $donation)
    {
        $donation->cancelled_at = now();
        $donation->cancelled_by_user_id = auth()->id();
        $donation->save();

        return response()->json([
            'message' => 'Donativo cancelado correctamente.',
            'data' => $donation
        ]);
    }

    public function export(Request $request)
    {
        $category = strtolower($request->query('category', ''));
        $activityId = $request->query('procuration_activity_id');

        if (!$activityId) {
            return response()->json(['error' => 'Es necesario especificar el id del evento (procuration_activity_id).'], 400);
        }

        // 1. Buscamos la actividad para obtener su nombre
        $activity = ProcurationActivity::find($activityId);

        // Si no se encuentra, usamos el ID como respaldo
        $activitySlug = $activity ? Str::slug($activity->name, '_') : "evento_{$activityId}";

        $query = Donation::query()->where('procuration_activity_id', $activityId);

        switch ($category) {
            case 'prospecto':
                // Reporte 1: Pagos realizados asignados a Prospectos
                $query->whereRaw('LOWER(source) = ?', ['prospecto']);
                $fileName = "reporte_prospectos_{$activitySlug}_" . now()->format('d-m-Y_His') . '.xlsx';
                $exportType = 'prospecto';
                break;

            case 'boteo':
                // Reporte 2: Registros de Boteo
                $query->whereRaw('LOWER(source) = ?', ['boteo']);
                $fileName = "reporte_boteo_{$activitySlug}_" . now()->format('d-m-Y_His') . '.xlsx';
                $exportType = 'boteo';
                break;

            case 'medios_eventos':
            case 'otros':
            case 'others':
                // Reporte 3: Llamadas, Redes Sociales, Templete, Bazar, Otros
                $sources = ['llamada', 'redes sociales', 'rrss', 'templete', 'bazar', 'otros', 'others'];
                $query->whereIn(DB::raw('LOWER(source)'), $sources);
                $fileName = "reporte_donativos_varios_{$activitySlug}_" . now()->format('d-m-Y_His') . '.xlsx';
                $exportType = 'others';
                break;

            case 'cobranza':
                // Reporte 4: Seguimiento a Cobranza
                $fileName = "reporte_cobranza_{$activitySlug}_" . now()->format('d-m-Y_His') . '.xlsx';
                $exportType = 'cobranza';
                break;

            default:
                return response()->json(['error' => 'Categoría de exportación no válida.'], 400);
        }

        $response = Excel::download(new DonationExport($query, $exportType), $fileName);

        $response->headers->set('Access-Control-Expose-Headers', 'Content-Disposition');

        return $response;
    }
}
