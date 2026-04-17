<?php

namespace Database\Seeders;

use App\Models\Kardex;
use App\Models\PlanCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /* $this->call(RoleSeeder::class);
        $this->call(WorkAreaSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(BrainLevelSeeder::class);
        $this->call(BrainFunctionSeeder::class);
        $this->call(InterviewQuestionSeeder::class);
        $this->call(ProgramSeeder::class);
        $this->call(CandidateSeeder::class);
        $this->call(KardexSeeder::class);
        $this->call(PlanCategorySeeder::class);
        $this->call(ActivityCategorySeeder::class);
        $this->call(GroupSeeder::class);
        $this->call(CandidateStatusSeeder::class);
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
        $user->roles()->attach( $evaluatorRole ); */

        /* Kardex::create([
            'name'     => 'Esquema de Vacunación',
            'slug'     => Str::slug('Esquema de Vacunación'),
            'category' => 'default',
            'order'    => 4
        ]);


        DB::statement("ALTER TABLE plan_categories AUTO_INCREMENT = 1;");

        PlanCategory::create([
            'id'        => 7,
            'label'     => 'Programa de Escucha',
            'name'      => Str::slug('Programa de Escucha', '_'),
            'parent_id' => null,
        ]); */

        $this->call(PlanTypeSeeder::class);
        $this->call(ActivitySeeder::class);
    }
}
