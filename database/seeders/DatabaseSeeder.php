<?php

namespace Database\Seeders;

use App\Models\InterviewQuestion;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

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

        $user2 = User::factory()->create([
            'name' => 'Pamela',
            'email' => 'pamela@sistemaenlac.com',
            'password' => bcrypt('123456'),
        ]);
        $user2->assignRole('evaluator');

        $user3 = User::factory()->create([
            'name' => 'Mario',
            'email' => 'mario@sistemaenlac.com',
            'password' => bcrypt('123456'),
        ]);
        $user3->assignRole('evaluator');

        $this->call(BrainLevelSeeder::class);
        $this->call(BrainFunctionSeeder::class);
        $this->call(InterviewQuestionSeeder::class);
        $this->call(ProgramSeeder::class);
        $this->call(CandidateSeeder::class);
    }
}
