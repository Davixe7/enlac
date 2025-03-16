<?php

namespace App\Services;

use App\Models\Interview;
use App\Models\Interviewee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InterviewService
{
    public function createInterview(Request $request)
    {
        // Iniciar transacción para asegurar la integridad de los datos
        return DB::transaction(function () use ($request) {
            // 1. Crear Interview
            $interview = Interview::create($request->interview);

            if ($request->hasFile('picture')) {
                $candidate->addMediaFromRequest('picture')->toMediaCollection('profile_picture');
            }

            // 2. Contactos
            foreach( $request->contacts as $contactData ){
                $candidate->contacts()->create($contactData);
            }

            // 3. Crear Medicamentos
            foreach ($request->medications as $medicationData) {
                $candidate->medications()->create($medicationData);
            }

            // 4. Crear agendamiento de Evaluacion
            if( $request->filled('evaluation_schedule') ){
                $candidate->evaluation_schedules()->create($request->evaluation_schedule);
            }

            return $candidate;
        });
    }

    public function updateCandidate(Candidate $candidate, Request $request)
    {
        return DB::transaction(function () use ($candidate, $request) {
            $candidateData = $request->candidate;
            unset($candidateData['id']);

            $candidate->update($candidateData);

            if ($request->hasFile('picture')) {
                $candidate->addMediaFromRequest('picture')->toMediaCollection('profile_picture');
            }

            // 2. Contactos
            if($request->filled('contacts')){
                foreach( $request->contacts as $contactData ){
                    $id = array_key_exists('id', $contactData) ? $contactData['id'] : null;
                    Contact::updateOrCreate(['id' => $id, 'candidate_id' => $candidate->id], $contactData);
                }
            }

            if($request->filled('medications')){
                foreach ($request->medications as $medicationData) {
                    Medication::updateOrCreate(
                        [
                            'id' => isset($medicationData['id']) ? $medicationData['id'] : null,
                            'candidate_id' => $candidate->id
                        ],
                        $medicationData
                    );
                }
            }

            /*
            Si cambian evaluador o fecha de cita
            cacelar cita actual, generar nueva cita. */
            if($request->filled('evaluation_schedule')){
                Storage::append('schedules.log', json_encode($request->evaluation_schedule));
                $evaluator_changed = $candidate->evaluation_schedule->evaluator_id != $request->evaluation_schedule['evaluator_id'];
                $date_changed      = $candidate->evaluation_schedule->date != $request->evaluation_schedule['date'];
                if ($evaluator_changed || $date_changed) {
                    $candidate->evaluation_schedule->update(['status' => 'canceled']);
                    $candidate->evaluation_schedules()->create([
                        'evaluator_id' => $request->evaluation_schedule['evaluator_id'],
                        'date' => $request->evaluation_schedule['date'],
                    ]);
                }
            }

            return $candidate;
        });
    }
}
