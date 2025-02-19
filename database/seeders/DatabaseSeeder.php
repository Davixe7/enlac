<?php

namespace Database\Seeders;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('123456')
        ]);

        $this->call(CandidateSeeder::class);
        $this->call(MedicationSeeder::class);
        $this->call(ContactSeeder::class);
        $this->call(AddressSeeder::class);
        $this->call(EvaluationSeeder::class);
        $this->call(BrainLevelSeeder::class);
        $this->call(BrainFunctionSeeder::class);
        $this->call(BrainFunctionRankSeeder::class);
    }
}
