<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AttendanceReportController extends Controller
{
    public function index(Request $request){
        $start = $request->start_date;
        $end   = $request->end_date;

        $daysCount = Carbon::parse($start)->diffInDaysFiltered(function($date){
            return !$date->isWeekend();
        }, Carbon::parse($end)->addDay());

        $data = Attendance::join('candidates', 'attendances.candidate_id', '=', 'candidates.id')
        ->where('type', 'daily')
        ->whereBetween('date', [$start, $end])
        ->whereRaw('WEEKDAY(date) < 5')
        ->selectRaw("
            candidate_id,
            CONCAT_WS(' ',
                candidates.first_name,
                NULLIF(candidates.middle_name, ''),
                NULLIF(candidates.last_name, '')
            ) as full_name,
            SUM( IF(attendances.status = 'present', 1, 0) ) as present,
            SUM( IF(attendances.status = 'absent' AND (absence_justification_comment IS NOT NULL AND absence_justification_comment != ''), 1, 0)) as justified,
            SUM( IF(attendances.status = 'absent' AND (absence_justification_comment IS NULL OR absence_justification_comment = ''), 1, 0)) as unjustified
        ")
        ->selectRaw("ROUND((SUM(IF(attendances.status = 'present', 1, 0)) / ?) * 100) as percentage", [$daysCount])
        ->groupBy('candidate_id', 'candidates.first_name', 'candidates.middle_name', 'candidates.last_name');

        if ($request->filled('candidate_id')) {
            $data = $data->where('attendances.candidate_id', $request->candidate_id);
        }

        $data = $data->get();

        $averagePercentage = round($data->avg('percentage'), 2);

        return response()->json(['data'=>[
            'rows' => $data,
            'average' => $averagePercentage,
            'days' => $daysCount
        ]]);
    }

    public function export(Request $request)
    {
        $start = $request->start_date;
        $end   = $request->end_date;

        $daysCount = Carbon::parse($start)->diffInDaysFiltered(function($date){
            return !$date->isWeekend();
        }, Carbon::parse($end)->addDay());

        // Aseguramos que no haya división por cero si el rango de fechas es inválido
        $daysCount = $daysCount ?: 1;

        $data = Attendance::join('candidates', 'attendances.candidate_id', '=', 'candidates.id')
        ->where('type', 'daily')
        ->whereBetween('date', [$start, $end])
        ->whereRaw('WEEKDAY(date) < 5')
        ->selectRaw("
            candidate_id,
            CONCAT_WS(' ', candidates.first_name, NULLIF(candidates.middle_name, ''), NULLIF(candidates.last_name, '')) as full_name,
            SUM( IF(attendances.status = 'present', 1, 0) ) as present,
            SUM( IF(attendances.status = 'absent' AND (absence_justification_comment IS NOT NULL AND absence_justification_comment != ''), 1, 0)) as justified,
            SUM( IF(attendances.status = 'absent' AND (absence_justification_comment IS NULL OR absence_justification_comment = ''), 1, 0)) as unjustified
        ")
        ->selectRaw("ROUND((SUM(IF(attendances.status = 'present', 1, 0)) / ?) * 100) as percentage", [$daysCount])
        ->groupBy('candidate_id', 'candidates.first_name', 'candidates.middle_name', 'candidates.last_name');

        if ($request->filled('candidate_id')) {
            $data = $data->where('attendances.candidate_id', $request->candidate_id);
        }

        $results = $data->get();
        $averagePercentage = round($results->avg('percentage'), 2);

        $rows = $results->map(function($item){
            return [
                'ID'          => $item->candidate_id,
                'Nombre'      => $item->full_name,
                'Presentes'   => $item->present,
                'Justificados'=> $item->justified,
                'Injustificados' => $item->unjustified,
                'Porcentaje'  => $item->percentage . '%',
            ];
        })->toArray();

        // Fila de resumen en español
        $rows[] = [
            'ID'          => 'PROMEDIO GENERAL',
            'Nombre'      => '',
            'Presentes'   => '',
            'Justificados'=> '',
            'Injustificados' => '',
            'Porcentaje'  => $averagePercentage . '%'
        ];

        $export = new class($rows) implements FromArray, WithHeadings, ShouldAutoSize {
            private $rows;
            public function __construct(array $rows){ $this->rows = $rows; }
            public function array(): array { return $this->rows; }

            // Encabezados en español para el cliente
            public function headings(): array {
                return [
                    'Folio',
                    'Nombre Completo',
                    'Días Presente',
                    'Faltas Justificadas',
                    'Faltas Injustificadas',
                    '% Asistencia'
                ];
            }
        };

        $filename = 'reporte_asistencia_' . $start . '_a_' . $end . '.xlsx';
        return Excel::download($export, $filename);
    }
}
