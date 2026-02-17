<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(WorkAreaSeeder::class);
        //$this->call(UserSeeder::class);
        $this->call(BrainLevelSeeder::class);
        $this->call(BrainFunctionSeeder::class);
        $this->call(InterviewQuestionSeeder::class);
        $this->call(ProgramSeeder::class);
        //$this->call(CandidateSeeder::class);
        //$this->call(KardexSeeder::class);
        $this->call(PlanCategorySeeder::class);
        //$this->call(ActivityCategorySeeder::class);
        //$this->call(GroupSeeder::class);
        //$this->call(CandidateStatusSeeder::class);
        $this->call(ActivitySeeder::class);

        $evaluatorRole = Role::whereName('evaluator')->first();
        $user = User::create([
            'name' => 'Dev',
            'last_name' => 'Enlac',
            'second_last_name' => 'Enlac',
            'email' => 'dev@gmail.com',
            'password' => bcrypt(123456),
            'work_area_id' => 1
        ]);
        $user->roles()->attach( $evaluatorRole );
    }
}
