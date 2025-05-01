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
        $users = [
            ['Maria', 'Salazar', 'Garcia'],
            ['Pamela', 'Guizar', ''],
            ['Mario', 'Garcia', ''],
        ];

        foreach($users as $u){
            $user = User::factory()->create([
                'name'             => $u[0],
                'last_name'        => $u[1],
                'second_last_name' => $u[2],
                'email'            => strtolower($u[0]) . '@sistemaenlac.com',
                'entry_date'       => now()
            ]);

            $user->assignRole('admin');
        }
    }
}
