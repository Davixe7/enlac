<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Exports\DonationReportExport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class DonationReportController extends Controller
{
    private function buildQuery(Request $request)
    {
        // Añadimos procurationActivity a la carga ansiosa (Eager Loading)
        $query = Donation::with([
            'donor:id,first_name,last_name,second_last_name',
            'sponsor:id,name,last_name,second_last_name,company_name',
            'procurationActivity:id,name'
        ]);

        if ($request->filled('date_from')) $query->whereDate('payment_date', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('payment_date', '<=', $request->date_to);

        if ($request->filled('search_donor')) {
            $search = $request->search_donor;
            $query->where(function ($q) use ($search) {
                $q->whereHas('donor', fn($s) => $s->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")->orWhere('second_last_name', 'like', "%{$search}%"))
                  ->orWhereHas('sponsor', fn($s) => $s->where('name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")->orWhere('second_last_name', 'like', "%{$search}%"));
            });
        }
        return $query;
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->buildQuery($request)->orderBy('payment_date', 'desc')->get()], 200);
    }

    public function export(Request $request)
    {
        $data = $this->buildQuery($request)->orderBy('payment_date', 'desc')->get();
        if ($data->isEmpty()) return response()->json(['message' => 'No hay datos'], 404);

        $fileName = 'Reporte_Donativos_' . Carbon::now()->format('d-m-Y_His') . '.xlsx';
        return Excel::download(new DonationReportExport($data), $fileName);
    }
}
