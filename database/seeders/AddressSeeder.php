<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = Contact::all();

        foreach ($contacts as $contact) {
            Address::create([
                'contact_id' => $contact->id,
                'street' => '123 Main St',
                'neighborhood' => 'Downtown',
                'state' => 'CA',
                'postal_code' => '90001',
                'exterior_number' => 'A1',
                'city' => 'Los Angeles',
                'country' => 'USA',
            ]);
        }
    }
}
