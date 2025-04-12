<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user1 = User::factory()->create([
            'name' => 'Maria',
            'last_name' => 'Salazar',
            'second_last_name' => 'Garcia',
            'phone' => '3211231230',
            'leader_id' => 1,
            'email' => 'test@example.com',
            'password' => bcrypt('123456'),
            'work_area_id'=> 1,
            'entry_date' => now()
        ]);

        /* $user2 = User::factory()->create([
            'name' => 'Pamela',
            'email' => 'pamela@sistemaenlac.com',
            'password' => bcrypt('123456'),
        ]);

        $user3 = User::factory()->create([
            'name' => 'Mario',
            'email' => 'mario@sistemaenlac.com',
            'password' => bcrypt('123456'),
        ]); */

        $evaluator_role = Role::where('name', 'like', "%evaluador%")->first();
        $user1->assignRole($evaluator_role);
        /* $user2->assignRole($evaluator_role);
        $user3->assignRole($evaluator_role); */
    }
}
