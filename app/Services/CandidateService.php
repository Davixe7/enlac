<?php

namespace App\Services;
use App\Models\Candidate;
use App\Models\Contact;
use App\Models\Address;
use App\Models\Medication;
use Illuminate\Http\Request;
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
            foreach ($request->medicamentos as $medicationData) {
                $medication = new Medication($medicationData);
                $medication->save();
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

            return $candidate;
        });
    }
}
