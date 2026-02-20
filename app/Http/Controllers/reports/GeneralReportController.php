<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\CandidateStatusLog;
use App\Models\Payment;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GeneralReportController extends Controller
{
    public function index(Request $request)
    {

        $start = $request->start_date;
        $end   = $request->end_date;

        $data  = [
            'candidates' => []
        ];

        $data['candidates']['evaluations']  = DB::table('evaluations')->whereBetween('created_at', [$start, $end])->distinct()->count('candidate_id');
        $data['candidates']['accepted']     = CandidateStatusLog::whereBetween('created_at', [$start, $end])->where('status', 'aceptado')->count();
        $data['candidates']['rejected']     = CandidateStatusLog::whereBetween('created_at', [$start, $end])->where('status', 'rechazado')->count();

        // Beneficiarios
        $data['beneficiaries']['active']    = CandidateStatusLog::whereBetween('created_at', [$start, $end])->where('status', 'activo')->count();
        $data['beneficiaries']['programed'] = CandidateStatusLog::whereBetween('created_at', [$start, $end])->where('status', 'programado')->count();

        $promedioTotal = DB::table('attendances')
            ->select(DB::raw("AVG(porcentaje_diario) as promedio_final"))
            ->fromSub(function ($query) use ($start, $end) {
                $query->from('attendances')
                    ->select('date')
                    ->selectRaw("(SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) as porcentaje_diario")
                    ->whereBetween('date', [$start, $end])
                    ->whereRaw('WEEKDAY(date) < 5') // Excluye Sábado (5) y Domingo (6)
                    ->where('type', 'daily')
                    ->groupBy('date');
            }, 'reporte_diario')
            ->value('promedio_final');
        $data['beneficiaries']['attendance'] = round($promedioTotal, 2);

        //Padrinos
        $data['sponsors']['total'] = Sponsor::count();
        $data['sponsors']['beneficiaries'] = Candidate::whereHas('sponsors')->count();
        $data['sponsors']['enlac'] = '?';

        //Tesoreria
        $data['payments']['parent'] = Payment::where('payment_type', 'parent')
            ->where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->sum('amount');

        $data['payments']['sponsor'] = Payment::where('payment_type', 'sponsor')
            ->where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->sum('amount');

        //Calcular diff in months de $start, $end
        //Obtener el programa de cada candidato activo en el rango de fecha
        //Para cada programa multiplicar el precio mensual * ceil(diffInMonths)
        //Sumar el monto anterior
        //Restar SUM(payments.amount) a monto anterior

        $data['rides']['equine'] = DB::table('rides')
            ->whereType('equine')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw("
                SUM(
                    IF(departure_time IS NOT NULL, 1, 0) + 
                    IF(return_time IS NOT NULL, 1, 0)
                ) as total
            ")
            ->value('total');

        $data['rides']['rubio'] = DB::table('rides')
            ->whereType('rubio')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw("
                SUM(
                    IF(departure_time IS NOT NULL, 1, 0) + 
                    IF(return_time IS NOT NULL, 1, 0)
                ) as total
            ")
            ->value('total');

            return response()->json(compact('data'));
    }

        public function export(Request $request)
        {
            $start = $request->start_date;
            $end   = $request->end_date;

            $data  = [
                'candidates' => []
            ];

            $data['candidates']['evaluations']  = DB::table('evaluations')->whereBetween('created_at', [$start, $end])->distinct()->count('candidate_id');
            $data['candidates']['accepted']     = CandidateStatusLog::whereBetween('created_at', [$start, $end])->where('status', 'aceptado')->count();
            $data['candidates']['rejected']     = CandidateStatusLog::whereBetween('created_at', [$start, $end])->where('status', 'rechazado')->count();

            $data['beneficiaries']['active']    = CandidateStatusLog::whereBetween('created_at', [$start, $end])->where('status', 'activo')->count();
            $data['beneficiaries']['programed'] = CandidateStatusLog::whereBetween('created_at', [$start, $end])->where('status', 'programado')->count();

            $promedioTotal = DB::table('attendances')
                ->select(DB::raw("AVG(porcentaje_diario) as promedio_final"))
                ->fromSub(function ($query) use ($start, $end) {
                    $query->from('attendances')
                        ->select('date')
                        ->selectRaw("(SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) as porcentaje_diario")
                        ->whereBetween('date', [$start, $end])
                        ->whereRaw('WEEKDAY(date) < 5')
                        ->where('type', 'daily')
                        ->groupBy('date');
                }, 'reporte_diario')
                ->value('promedio_final');
            $data['beneficiaries']['attendance'] = round($promedioTotal, 2);

            $data['sponsors']['total'] = Sponsor::count();
            $data['sponsors']['beneficiaries'] = Candidate::whereHas('sponsors')->count();
            $data['sponsors']['enlac'] = '?';

            $data['payments']['parent'] = Payment::where('payment_type', 'parent')
                ->where('date', '>=', $start)
                ->where('date', '<=', $end)
                ->sum('amount');

            $data['payments']['sponsor'] = Payment::where('payment_type', 'sponsor')
                ->where('date', '>=', $start)
                ->where('date', '<=', $end)
                ->sum('amount');

            $data['rides']['equine'] = DB::table('rides')
                ->whereType('equine')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw("SUM( IF(departure_time IS NOT NULL, 1, 0) + IF(return_time IS NOT NULL, 1, 0) ) as total")
                ->value('total');

            $data['rides']['rubio'] = DB::table('rides')
                ->whereType('rubio')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw("SUM( IF(departure_time IS NOT NULL, 1, 0) + IF(return_time IS NOT NULL, 1, 0) ) as total")
                ->value('total');

            $rows = [];
            $rows[] = ['Metric' => 'candidates.evaluations', 'Value' => $data['candidates']['evaluations']];
            $rows[] = ['Metric' => 'candidates.accepted', 'Value' => $data['candidates']['accepted']];
            $rows[] = ['Metric' => 'candidates.rejected', 'Value' => $data['candidates']['rejected']];
            $rows[] = ['Metric' => 'beneficiaries.active', 'Value' => $data['beneficiaries']['active']];
            $rows[] = ['Metric' => 'beneficiaries.programed', 'Value' => $data['beneficiaries']['programed']];
            $rows[] = ['Metric' => 'beneficiaries.attendance', 'Value' => $data['beneficiaries']['attendance']];
            $rows[] = ['Metric' => 'sponsors.total', 'Value' => $data['sponsors']['total']];
            $rows[] = ['Metric' => 'sponsors.beneficiaries', 'Value' => $data['sponsors']['beneficiaries']];
            $rows[] = ['Metric' => 'payments.parent', 'Value' => $data['payments']['parent']];
            $rows[] = ['Metric' => 'payments.sponsor', 'Value' => $data['payments']['sponsor']];
            $rows[] = ['Metric' => 'rides.equine', 'Value' => $data['rides']['equine']];
            $rows[] = ['Metric' => 'rides.rubio', 'Value' => $data['rides']['rubio']];

            $export = new class($rows) implements FromArray, WithHeadings, ShouldAutoSize {
                private $rows;
                public function __construct(array $rows){ $this->rows = $rows; }
                public function array(): array { return $this->rows; }
                public function headings(): array { return ['Metric','Value']; }
            };

            $filename = 'general_report_' . $start . '_' . $end . '_' . time() . '.xlsx';
            return Excel::download($export, $filename);
        }
}
