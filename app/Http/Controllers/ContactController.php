<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContactResource;
use App\Models\Candidate;
use App\Models\Contact;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $contacts = Contact::whereCandidateId($request->candidate_id)->get();
        return ContactResource::collection($contacts);
    }

    public function store(StoreContactRequest $request)
    {
        $candidate = Candidate::where('id', $request->validated()->candidate_id);
        $contact = $candidate->contacts()->create($request->validated());
        return new ContactResource($contact);
    }

    public function show(Contact $contact)
    {
        return new ContactResource($contact);
    }

    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $contact = $contact->update($request->validated());
        return new ContactResource($contact);
    }

    public function destroy(Contact $contact)
    {
        $contact = $contact->delete();
        return response()->json(['data' => $contact]);
    }
}
