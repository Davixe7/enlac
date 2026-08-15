<?php

namespace App\Http\Controllers;

use App\Exports\DonationExport;
use App\Models\Donation;
use App\Models\ProcurationActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class DonationExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $category = strtolower($request->query('category', ''));
        $activityId = $request->query('procuration_activity_id');

        if (!$activityId) {
            return response()->json(['error' => 'Es necesario especificar el id del evento (procuration_activity_id).'], 400);
        }

        $activity = ProcurationActivity::find($activityId);
        $activitySlug = $activity ? Str::slug($activity->name, '_') : "evento_{$activityId}";

        $query = Donation::query()->where('procuration_activity_id', $activityId);

        switch ($category) {
            case 'prospecto':
                $query->whereRaw('LOWER(source) = ?', ['prospecto']);
                $fileName = "reporte_prospectos_{$activitySlug}_" . now()->format('d-m-Y_His') . '.xlsx';
                $exportType = 'prospecto';
                break;

            case 'boteo':
                $query->whereRaw('LOWER(source) = ?', ['boteo']);
                $fileName = "reporte_boteo_{$activitySlug}_" . now()->format('d-m-Y_His') . '.xlsx';
                $exportType = 'boteo';
                break;

            case 'medios_eventos':
            case 'otros':
            case 'others':
                $sources = ['llamada', 'redes sociales', 'rrss', 'templete', 'bazar', 'otros', 'others'];
                $query->whereIn(DB::raw('LOWER(source)'), $sources);
                $fileName = "reporte_donativos_varios_{$activitySlug}_" . now()->format('d-m-Y_His') . '.xlsx';
                $exportType = 'others';
                break;

            case 'cobranza':
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
