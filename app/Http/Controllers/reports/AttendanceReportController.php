<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
}
