<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BeneficiaryAttendanceReportController extends Controller
{
    public function daily(Request $request){
        $request->validate(['candidate_id'=>'required']);
        $date = $request->date ?: now();

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

        return response()->json(['data'=>$rows]);
    }
}
