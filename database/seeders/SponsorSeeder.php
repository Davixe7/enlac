<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\PaymentConfig;
use App\Models\Sponsor;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SponsorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sponsor = Sponsor::create([
            'name' => 'Padrino',
            'last_name' => 'Apellido',
            'second_last_name' => 'Dos',
            'birthdate' => Carbon::now()->subYears(20),
            'marital_status' => 'Casado(a)',
            'razon_social' => 'Padrinos C.A',
        ]);

        $sponsor->candidates->attach([1]);

        $paymentConfig = PaymentConfig::create([
            'sponsor_id' => $sponsor->id,
            'candidate_id' => 1,
            'amount' => 6000,
            'frequency' => 4
        ]);

        Payment::create([
            'sponsor_id' => 1,
            'candidate_id' => 1,
            'date' => Carbon::now(),
            'amount' => 1000
        ]);
    }
}
