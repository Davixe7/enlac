<?php

namespace App\Services;
use App\Models\Candidate;
use App\Models\Contact;
use App\Models\Address;
use App\Models\Medication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CandidateService
{
    public function createCandidate(Request $request)
    {
        // Iniciar transacción para asegurar la integridad de los datos
        return DB::transaction(function () use ($request) {
            // 1. Crear Candidato
            $candidate = Candidate::create($request->all());

            // 2. Crear Contactos
            foreach ($request->input('contacts', []) as $contactData) {
                $contact = new Contact($contactData);
                $contact->candidate_id = $candidate->id;
                $contact->save();

                // 3. Crear Direcciones para cada Contacto
                foreach ($contactData['addresses'] as $addressData) {
                    $address = new Address($addressData);
                    $address->contact_id = $contact->id;
                    $address->save();
                }
            }

            // 4. Crear Medicamentos
            foreach ($request->input('medications', []) as $medicationData) {
                $medication = new Medication($medicationData);
                $medication->candidate_id = $candidate->id;
                $medication->save();
            }

            return $candidate;
        });
    }
}
