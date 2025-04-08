<?php

namespace Database\Seeders;

use App\Models\InterviewQuestion;
use Illuminate\Support\Str;
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
        $role = Role::create(['name' => 'evaluator']);
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('123456'),
        ]);
        $user->assignRole($role);

        $user2 = User::factory()->create([
            'name' => 'Pamela',
            'email' => 'pamela@sistemaenlac.com',
            'password' => bcrypt('123456'),
        ]);
        $user2->assignRole($role);

        $user3 = User::factory()->create([
            'name' => 'Mario',
            'email' => 'mario@sistemaenlac.com',
            'password' => bcrypt('123456'),
        ]);
        $user3->assignRole('evaluator');

        $areas = ['Medico', 'Nutrición', 'Psicología', 'Comunicación', 'Programa escucha'];
        foreach( $areas as $area ){
            Role::create(['name' => $area]);
            $user = User::factory()->create([
                'name' => $area,
                'email' => Str::slug($area) . '@example.com',
                'password' => bcrypt('123456'),
            ]);
            $user->assignRole($area);
        }

        $this->call(BrainLevelSeeder::class);
        $this->call(BrainFunctionSeeder::class);
        $this->call(InterviewQuestionSeeder::class);
        $this->call(ProgramSeeder::class);
        $this->call(CandidateSeeder::class);
    }
}
