<?php

namespace Database\Seeders;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $role = Role::create(['name' => 'evaluator']);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('123456'),
        ]);

        $user->assignRole('evaluator');

        /* $this->call(CandidateSeeder::class);
        $this->call(MedicationSeeder::class);
        $this->call(ContactSeeder::class);
        $this->call(AddressSeeder::class);t
        $this->call(EvaluationSeeder::class);
        $this->call(BrainLevelSeeder::class);
        $this->call(BrainFunctionSeeder::class);
        $this->call(BrainFunctionRankSeeder::class);
        $this->call(ProgramSeeder::class);
        $this->call(InterviewSeeder::class);
        $this->call(InterviewQuestionSeeder::class); */
    }
}
