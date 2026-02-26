<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BeneficiaryAttendanceReportController extends Controller
{
    public function daily(Request $request){
        $request->validate(['candidate_id'=>'required']);
        $date = $request->date ?: now();

        $rows = DB::table('attendances')
        ->whereCandidateId($request->candidate_id)
        ->where('date', '=', $date)
        ->where('type', '=', 'area')
        ->join('plan_categories', 'plan_categories.id', '=', 'attendances.plan_category_id')
        ->select([
            'attendances.plan_category_id',
            'plan_categories.label as area_name',
            'attendances.status as attendance_status',
            'attendances.date as attendance_date',
            'absence_justification_type',
        ])
        ->get();

        return response()->json(['data'=>$rows]);
    }

    public function export(Request $request)
    {
        $request->validate(['candidate_id'=>'required']);
        $date = $request->date ?: now()->toDateString();

        $rows = DB::table('candidates')
        ->join('candidate_group', 'candidates.id', '=', 'candidate_group.candidate_id')
        ->join('groups', 'groups.id', '=', 'candidate_group.group_id')
        ->join('plans', 'plans.group_id', '=', 'groups.id')
        ->join('plan_categories', 'plan_categories.id', '=', 'plans.category_id')
        ->leftJoin('attendances', 'attendances.plan_category_id', '=', 'plan_categories.id')
        ->where('candidates.id', $request->candidate_id)
        ->where('date', $date)
        ->select([
            'plan_categories.label as area_name',
            'attendances.date as attendance_date',
            'attendances.status as attendance_status',
            'attendances.id as attendance_id',
            'attendances.absence_justification_type',
        ])
        ->get();

        $exportRows = $rows->map(function($r){
            return [
                'area_name' => $r->area_name,
                'attendance_date' => $r->attendance_date,
                'attendance_status' => $r->attendance_status,
                'attendance_id' => $r->attendance_id,
                'absence_justification_type' => $r->absence_justification_type,
            ];
        })->toArray();

        $export = new class($exportRows) implements FromArray, WithHeadings, ShouldAutoSize {
            private $rows;
            public function __construct(array $rows){ $this->rows = $rows; }
            public function array(): array { return $this->rows; }
            public function headings(): array { return ['area_name','attendance_date','attendance_status','attendance_id','absence_justification_type']; }
        };

        $filename = 'beneficiary_attendance_' . $request->candidate_id . '_' . $date . '_' . time() . '.xlsx';
        return Excel::download($export, $filename);
    }
}
