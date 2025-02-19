<?php

namespace Database\Seeders;

use App\Models\Candidate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Candidate::create([
            'first_name' => 'John',
            'middle_name' => 'Doe',
            'last_name' => 'Smith',
            'birth_date' => '1990-01-01',
            'age' => 35,
            'chronological_age' => 35,
            'diagnosis' => 'Diagnosis A',
            'photo' => 'photo1.jpg',
        ]);

        Candidate::create([
            'first_name' => 'Jane',
            'middle_name' => 'Marie',
            'last_name' => 'Doe',
            'birth_date' => '1985-02-15',
            'age' => 40,
            'chronological_age' => 40,
            'diagnosis' => 'Diagnosis B',
            'photo' => 'photo2.jpg',
        ]);

        Candidate::create([
            'first_name' => 'Alice',
            'middle_name' => 'Emily',
            'last_name' => 'Johnson',
            'birth_date' => '1995-05-20',
            'age' => 30,
            'chronological_age' => 30,
            'diagnosis' => 'Diagnosis C',
            'photo' => 'photo3.jpg',
        ]);

        Candidate::create([
            'first_name' => 'Bob',
            'middle_name' => 'Michael',
            'last_name' => 'Brown',
            'birth_date' => '1980-08-10',
            'age' => 45,
            'chronological_age' => 45,
            'diagnosis' => 'Diagnosis D',
            'photo' => 'photo4.jpg',
        ]);

        Candidate::create([
            'first_name' => 'Charlie',
            'middle_name' => 'David',
            'last_name' => 'Wilson',
            'birth_date' => '2000-12-05',
            'age' => 25,
            'chronological_age' => 25,
            'diagnosis' => 'Diagnosis E',
            'photo' => 'photo5.jpg',
        ]);
    }
}
