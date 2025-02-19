<?php

namespace Database\Seeders;

use App\Models\Medication;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Medication::create([
            'name' => 'Paracetamol',
            'dose' => '500mg',
            'frequency' => '2 times a day',
            'duration' => '7 days',
        ]);

        Medication::create([
            'name' => 'Ibuprofen',
            'dose' => '400mg',
            'frequency' => '3 times a day',
            'duration' => '5 days',
        ]);

        Medication::create([
            'name' => 'Amoxicillin',
            'dose' => '250mg',
            'frequency' => '2 times a day',
            'duration' => '10 days',
        ]);

        Medication::create([
            'name' => 'Metformin',
            'dose' => '850mg',
            'frequency' => '2 times a day',
            'duration' => '1 month',
        ]);

        Medication::create([
            'name' => 'Simvastatin',
            'dose' => '20mg',
            'frequency' => 'once a day',
            'duration' => '1 month',
        ]);
    }
}
