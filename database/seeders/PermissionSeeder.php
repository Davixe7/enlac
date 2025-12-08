<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'beneficiaries.update'],
            ['name' => 'kardexes.update'],
            ['name' => 'appointments.update'],
            ['name' => 'payment_configs.update'],
            ['name' => 'sponsors.update'],
            ['name' => 'payments.update'],
        ];

        foreach ($permissions as $permission) {
            $permission['guard_name'] = 'sanctum';
            Permission::create($permission);
        }

        Role::find(1)->permissions()->sync([1,2,3,4,5,6]);
        Role::find(3)->permissions()->sync([1,2,3,4,6]);
        Role::find(6)->permissions()->sync([1,2,3,4,5]);
        //Role::find(10)->permissions()->sync([1,2,3,4,5]);
    }
}
