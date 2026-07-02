<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //$this->call(RoleSeeder::class);
        //$this->call(WorkAreaSeeder::class);
        //$this->call(UserSeeder::class);
        //$this->call(BrainLevelSeeder::class);
        //$this->call(BrainFunctionSeeder::class);
        //$this->call(InterviewQuestionSeeder::class);
        //$this->call(ProgramSeeder::class);
        //$this->call(CandidateSeeder::class);
        //$this->call(KardexSeeder::class);
        //$this->call(PlanCategorySeeder::class);
        //$this->call(ActivityCategorySeeder::class);
        //$this->call(GroupSeeder::class);
        //$this->call(ActivitySeeder::class);
        //$this->call(PermissionSeeder::class);
        $this->call(RadiomarathonKeySeeder::class);

        //$evaluatorRole = Role::whereName('evaluator')->first();
        //$adminRole     = Role::whereName('admin')->first();
        //$user->roles()->attach( [$evaluatorRole, $adminRole] );

        Program::whereDoesntHave('programStatusLogs')
        ->get()
        ->each(function($p){
            $p->programStatusLogs()->create(['is_active'=>1, 'user_id' => 1]);
        });
    }
}
