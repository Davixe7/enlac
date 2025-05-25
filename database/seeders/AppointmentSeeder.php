<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Candidate;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $candidates = Candidate::all();
        $users = User::all();

        foreach ($candidates as $candidate) {
            foreach ($users as $user) {
                Appointment::create([
                    'candidate_id' => $candidate->id,
                    'evaluator_id' => $user->id,
                    'type_id'      => 1,
                    'date'         => '2024-05-30 12:00:00',
                    'observation'  => 'Paciente presenta buen estado de salud.',
                ]);
            }
        }
    }
}
