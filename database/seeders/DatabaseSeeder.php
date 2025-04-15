<?php

namespace Database\Seeders;

use App\Models\InterviewQuestion;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\WorkArea;
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
        /* $this->call(RoleSeeder::class);
        $this->call(WorkAreaSeeder::class); */
        /* $this->call(UserSeeder::class);
        $this->call(BrainLevelSeeder::class);
        $this->call(BrainFunctionSeeder::class);
        $this->call(InterviewQuestionSeeder::class);
        $this->call(ProgramSeeder::class);
        $this->call(CandidateSeeder::class); */
        $this->call(SponsorSeeder::class);
    }
}
