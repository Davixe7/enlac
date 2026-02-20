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

        $data = $data->get();
        $averagePercentage = round($data->avg('percentage'), 2);

        $rows = $data->map(function($item){
            return [
                'candidate_id' => $item->candidate_id,
                'full_name' => $item->full_name,
                'present' => $item->present,
                'justified' => $item->justified,
                'unjustified' => $item->unjustified,
                'percentage' => $item->percentage,
            ];
        })->toArray();

        // add summary row
        $rows[] = ['candidate_id' => 'AVERAGE', 'full_name' => '', 'present' => '', 'justified' => '', 'unjustified' => '', 'percentage' => $averagePercentage];

        $export = new class($rows) implements FromArray, WithHeadings, ShouldAutoSize {
            private $rows;
            public function __construct(array $rows){ $this->rows = $rows; }
            public function array(): array { return $this->rows; }
            public function headings(): array { return ['candidate_id','full_name','present','justified','unjustified','percentage']; }
        };

        $filename = 'attendance_report_' . $start . '_' . $end . '_' . time() . '.xlsx';
        return Excel::download($export, $filename);
    }
}
