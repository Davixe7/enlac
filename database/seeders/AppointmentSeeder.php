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
                    'appointment_type' => 'entrevista',
                    'user_id' => $user->id,
                    'date' => '2024-05-15',
                    'time_slot' => '10:00-11:00',
                    'observation' => 'Paciente presenta buen estado de salud.',
                ]);
            }
        }
    }
}
