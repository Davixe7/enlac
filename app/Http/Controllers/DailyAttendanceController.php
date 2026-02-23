<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class DailyAttendanceController extends Controller
{
    public function store(Request $request){
        $data = $request->validate([
            'candidate_id'                  => 'required',
            'date'                          => 'date',
            'absence_justification_type'    => 'nullable',
            'absence_justification_comment' => 'required_with:absence_justification_type',
        ]);

        $data['status'] = 'absent';
        $search = $request->only('candidate_id', 'type', 'status', 'date');

        $data = Attendance::updateOrCreate($search, [
            'absence_justification_type'    => $request->absence_justification_type,
            'absence_justification_comment' => $request->absence_justification_comment
        ]);

        $data->candidate->attendances()
        ->whereType('area')
        ->whereStatus('absent')
        ->whereDate('date', $request->date)
        ->update($request->only('absence_justification_comment'));

        return response()->json(compact('data'));
    }
}
