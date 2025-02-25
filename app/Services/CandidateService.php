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

            // 2. Crear Contacto
            $contact = new Contact($request->contact);
            $contact->candidate_id = $candidate->id;
            $contact->save();

            // 3. Crear Domicilio
            $address = new Address($request->address);
            $address->contact_id = $contact->id;
            $address->save();

            // 4. Crear Medicamentos
            foreach ($request->medications as $medicationData) {
                $candidate->medications()->create($medicationData);
            }

            // 5. Crear agendamiento de Evaluacion
            $evaluation_schedule = EvaluationSchedule::create(array_merge(
                $request->evaluation_schedule,
                ['candidate_id' => $candidate->id]
            ));

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

    public function admission(Request $request, Candidate $candidate){
        return DB::transaction(function () use ($request, $candidate) {

            $candidate->update([
                'acceptance_status' => $request->acceptance_status,
                'rejection_comment' => $request->rejection_comment
            ]);

            if($request->filled('programs')){
                $candidate->programs()->sync($request->programs);
            }

            return $candidate;
        });
    }
}
