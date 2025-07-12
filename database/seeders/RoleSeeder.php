<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'admin',          'label' => 'Administrador General'],
            ['name' => 'evaluator',      'label' => 'Evaluador'],
            ['name' => 'reception',      'label' => 'Recepción'],
            ['name' => 'manager',        'label' => 'Gestión del Beneficiario'],
            ['name' => 'coord_physical', 'label' => 'Coordinación Física'],
            ['name' => 'coord_academic', 'label' => 'Coordinación Académica']
        ];

        foreach ($roles as $role) {
            $role['guard_name'] = 'sanctum';
            Role::create($role);
        }
    }
}
