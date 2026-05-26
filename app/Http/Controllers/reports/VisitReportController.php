<?php

namespace App\Http\Controllers\Reports;

use App\Exports\DonorVisitReportExport;
use App\Http\Controllers\Controller;
use App\Models\DonorVisit;
use App\Models\Donation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;

class VisitReportController extends Controller
{
    private function buildQuery(Request $request)
    {
        $visitsTable = (new DonorVisit)->getTable();

        return DonorVisit::with([
            'responsible:id,name,last_name,second_last_name',
            'donor:id,first_name,last_name,second_last_name,company_name,sector'
        ])
        ->addSelect(['last_radiomaraton_amount' => Donation::select('amount')
            ->whereColumn('donor_id', "{$visitsTable}.donor_id")
            ->where('activity_type', 'Radiomaratón')
            ->orderBy('payment_date', 'desc')
            ->limit(1)
        ])
        ->when($request->filled('date_from'), fn($q) => $q->whereDate('visit_date', '>=', $request->date_from))
        ->when($request->filled('date_to'), fn($q) => $q->whereDate('visit_date', '<=', $request->date_to))
        ->when($request->filled('activity_type'), function ($q) use ($request) {
            $q->whereHas('donor', fn($sub) => $sub->where('prospect_for', 'like', "%{$request->activity_type}%"));
        });
    }

    public function index(Request $request): JsonResponse
    {
        $visits = $this->buildQuery($request)->orderBy('visit_date', 'desc')->get();
        return response()->json(['data' => $visits], 200);
    }

    public function export(Request $request)
    {
        $data = $this->buildQuery($request)->orderBy('visit_date', 'desc')->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'No hay datos para exportar'], 404);
        }

        $fileName = 'Reporte_Visitas_' . Carbon::now()->format('d-m-Y_His') . '.xlsx';

        return Excel::download(new DonorVisitReportExport($data), $fileName);
    }
}
