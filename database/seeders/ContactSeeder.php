<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $candidate = Candidate::first();

        Contact::create([
            'candidate_id' => $candidate->id,
            'first_name' => 'John',
            'middle_name' => 'Doe',
            'last_name' => 'Smith',
            'relationship' => 'Father',
            'enlac_responsible' => true,
            'legal_guardian' => true,
            'email' => 'john.doe@example.com',
            'whatsapp' => '+1234567890',
            'home_phone' => '+0987654321',
        ]);

        Contact::create([
            'candidate_id' => $candidate->id,
            'first_name' => 'Jane',
            'middle_name' => 'Marie',
            'last_name' => 'Doe',
            'relationship' => 'Mother',
            'enlac_responsible' => false,
            'legal_guardian' => true,
            'email' => 'jane.doe@example.com',
            'whatsapp' => '+1234567891',
            'home_phone' => '+0987654322',
        ]);

        Contact::create([
            'candidate_id' => $candidate->id,
            'first_name' => 'Alice',
            'middle_name' => 'Emily',
            'last_name' => 'Johnson',
            'relationship' => 'Aunt',
            'enlac_responsible' => true,
            'legal_guardian' => false,
            'email' => 'alice.johnson@example.com',
            'whatsapp' => '+1234567892',
            'home_phone' => '+0987654323',
        ]);

        Contact::create([
            'candidate_id' => $candidate->id,
            'first_name' => 'Bob',
            'middle_name' => 'Michael',
            'last_name' => 'Brown',
            'relationship' => 'Uncle',
            'enlac_responsible' => false,
            'legal_guardian' => false,
            'email' => 'bob.brown@example.com',
            'whatsapp' => '+1234567893',
            'home_phone' => '+0987654324',
        ]);

        Contact::create([
            'candidate_id' => $candidate->id,
            'first_name' => 'Charlie',
            'middle_name' => 'David',
            'last_name' => 'Wilson',
            'relationship' => 'Brother',
            'enlac_responsible' => true,
            'legal_guardian' => false,
            'email' => 'charlie.wilson@example.com',
            'whatsapp' => '+1234567894',
            'home_phone' => '+0987654325',
        ]);
    }
}
