<?php

namespace App\Services;

use App\Http\Requests\StoreSponsorRequest;
use App\Models\Sponsor;
use App\Models\Contact;
use App\Models\Address;
use App\Models\EvaluationSchedule;
use App\Models\Medication;
use App\Models\User;
use App\Notifications\EvaluationScheduled;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SponsorService
{
    public function createSponsor(StoreSponsorRequest $request)
    {
        // Iniciar transacción para asegurar la integridad de los datos
        return DB::transaction(function () use ($request) {
            // 1. Crear Padrino
            $sponsor = Sponsor::create($request->validated()['sponsor']);

            // 2. Domicilios
            foreach( $request->addresses as $address ){
                $sponsor->addresses()->create($address);
            }

            return $sponsor;
        });
    }

    public function updateSponsor(Sponsor $sponsor, Request $request)
    {
        return DB::transaction(function () use ($sponsor, $request) {
            $sponsorData = $request->sponsor;
            unset($sponsorData['id']);

            $sponsor->update($sponsorData);

            if ($request->hasFile('picture')) {
                $sponsor->addMediaFromRequest('picture')->toMediaCollection('profile_picture');
            }

            // 2. Contactos
            if($request->filled('contacts')){
                foreach( $request->contacts as $contactData ){
                    $id = array_key_exists('id', $contactData) ? $contactData['id'] : null;
                    Contact::updateOrCreate(['id' => $id, 'sponsor_id' => $sponsor->id], $contactData);
                }
            }

            if($request->filled('medications')){
                foreach ($request->medications as $medicationData) {
                    Medication::updateOrCreate(
                        [
                            'id' => isset($medicationData['id']) ? $medicationData['id'] : null,
                            'sponsor_id' => $sponsor->id
                        ],
                        $medicationData
                    );
                }
            }

            /*
            Si cambian evaluador o fecha de cita
            cancelar cita actual, generar nueva cita. */
            if($request->filled('evaluation_schedule')){
                $evaluator_changed = $sponsor->evaluation_schedule->evaluator_id != $request->evaluation_schedule['evaluator_id'];
                $date_changed      = $sponsor->evaluation_schedule->date != $request->evaluation_schedule['date'];

                if (!$evaluator_changed && !$date_changed) { return; }

                $sponsor->evaluation_schedule->update(['status' => 'canceled']);
                $schedule = $sponsor->appointments()->create([
                    'type_id' => 0,
                    'evaluator_id' => $request->evaluation_schedule['evaluator_id'],
                    'date' => $request->evaluation_schedule['date'],
                ]);
                $schedule->evaluator->notify( new EvaluationScheduled( $schedule ) );
            }

            return $sponsor;
        });
    }
}
