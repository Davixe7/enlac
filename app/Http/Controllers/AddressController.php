<?php

namespace App\Http\Controllers;

use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;

class AddressController extends Controller
{
    public function index(Contact $contact)
    {
        return AddressResource::collection($contact->addresses());
    }

    public function store(StoreAddressRequest $request)
    {
        $contact = Contact::where('id', $request->validated()->contact_id);
        $address = $contact->addresses()->create($request->validated());

        return new AddressResource($address);
    }

    public function show(Address $address)
    {
        return new AddressResource($address);
    }

    public function update(UpdateAddressRequest $request, Address $address)
    {
        $address = $address->update($request->validated());
        return new AddressResource($address);
    }

    public function destroy(Address $address)
    {
        $address = $address->delete();
        return response()->json(['data' => $address]);
    }
}
