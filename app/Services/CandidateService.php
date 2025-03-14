<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Contact;
use App\Models\Address;
use App\Models\EvaluationSchedule;
use App\Models\Medication;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CandidateService
{
    public function createCandidate(Request $request)
    {
        // Iniciar transacción para asegurar la integridad de los datos
        return DB::transaction(function () use ($request) {
            // 1. Crear Candidato
            $candidate = Candidate::create($request->candidate);

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

            $contact = Contact::updateOrCreate(['id' => $request->contact['id']], $request->contact);
            $address = Address::updateOrCreate(['id' => $request->address], $request->address);

            foreach ($request->medications as $medicationData) {
                Medication::updateOrCreate(
                    [
                        'id' => isset($medicationData['id']) ? $medicationData['id'] : null,
                        'candidate_id' => $candidate->id
                    ],
                    $medicationData
                );
            }

            /*
            Si cambian evaluador o fecha de cita
            cacelar cita actual, generar nueva cita. */
            $evaluator_changed = $candidate->evaluation_schedule['evaluator_id'] != $request->evaluation_schedule['evaluator_id'];
            $date_changed      = $candidate->evaluation_schedule['date'] != $request->evaluation_schedule['date'];
            if ($evaluator_changed || $date_changed) {
                $candidate->evaluation_schedule->update(['status' => 'canceled']);
                $evaluation_schedule = EvaluationSchedule::create([
                    "candidate_id" => $request->evaluation_schedule['candidate_id'],
                    "evaluator_id" => $request->evaluation_schedule['evaluator_id'],
                    "date" => $request->evaluation_schedule['date'],
                ]);
            }

            return $candidate;
        });
    }
}
