<?php

namespace App\Services;

use App\Http\Requests\StoreCandidateRequest;
use App\Models\Candidate;
use App\Models\Contact;
use App\Models\Address;
use App\Models\EvaluationSchedule;
use App\Models\Group;
use App\Models\Medication;
use App\Models\User;
use App\Notifications\EvaluationScheduled;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CandidateService
{
    public function createCandidate(StoreCandidateRequest $request)
    {
        // Iniciar transacción para asegurar la integridad de los datos
        return DB::transaction(function () use ($request) {
            // 1. Crear Candidato
            $candidate = Candidate::create($request->validated()['candidate']);

            if ($request->hasFile('picture')) {
                $candidate->addMediaFromRequest('picture')->toMediaCollection('profile_picture');
            }

            // 2. Contactos
            foreach( $request->contacts as $contactData ){
                $candidate->contacts()->create($contactData);
            }

            // 3. Crear Medicamentos
            if( $request->filled('medications') ){
                foreach ($request->medications as $medicationData) {
                    $candidate->medications()->create($medicationData);
                }
            }

            // 4. Crear agendamiento de Evaluacion
            if( $request->filled('evaluation_schedule') ){
                $schedule = $candidate->appointments()->create($request->evaluation_schedule);
                $schedule->evaluator->notify( new EvaluationScheduled( $schedule ) );
            }

            // 5. Crear Grupo Individual para actividades posteriores
            if( $candidate ){
                $group = Group::create(['name' => $candidate->full_name, 'is_individual' => 1]);
                $group->candidates()->attach([$candidate->id]);
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
            cancelar cita actual, generar nueva cita. */
            if($request->filled('evaluation_schedule')){
                $evaluator_changed = $candidate->evaluation_schedule->evaluator_id != $request->evaluation_schedule['evaluator_id'];
                $date_changed      = $candidate->evaluation_schedule->date != $request->evaluation_schedule['date'];

                if (!$evaluator_changed && !$date_changed) { return; }

                $candidate->evaluation_schedule->update(['status' => 'canceled']);
                $schedule = $candidate->appointments()->create([
                    'type_id' => 0,
                    'evaluator_id' => $request->evaluation_schedule['evaluator_id'],
                    'date' => $request->evaluation_schedule['date'],
                ]);
                $schedule->evaluator->notify( new EvaluationScheduled( $schedule ) );
            }

            return $candidate;
        });
    }

    

}
