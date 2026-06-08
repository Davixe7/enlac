<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Resources\EventsCalendarResources;
use App\Models\Group;
use App\Models\EventCalendar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Enums\CandidateStatus;
use App\Models\Appointment;
use App\Models\WorkArea;

use App\Http\Resources\AppointmentResource;
use App\Models\MedicalRecords;
use App\Services\CandidateService;

class EventsCalendarController extends Controller
{
    public function index() {
        $Appointment = Appointment::with(['evaluator','candidate','MedicalRecords'])->whereIn('type_id', [1, 2, 3])->orderBy('date')->get();
        $data = EventsCalendarResources::collection($Appointment);
        
            return response()->json(compact('data'));
    }
}