<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $this->call(KardexSeeder::class);
    }
}
