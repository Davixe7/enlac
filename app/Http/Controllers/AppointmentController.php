<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Http\Resources\AppointmentResource;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Notifications\AppointmentScheduled;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $appointments = Appointment::byCandidate($request->candidate_id)
        ->with('evaluator')
        ->orderBy('date')
        ->get();

        return AppointmentResource::collection($appointments);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $data = $request->validated();
        $appointment = Appointment::create($data);
        $appointment->evaluator->notify(new AppointmentScheduled($appointment));
        return new AppointmentResource($appointment);
    }

    public function show(Appointment $appointment)
    {
        return new AppointmentResource($appointment);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $appointment->update($request->validated());
        return new AppointmentResource($appointment);
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return response()->json(['data' => $appointment], 204);
    }
}
