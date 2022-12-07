<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::firstOrCreate(['name' => 'create-users']);
        Permission::firstOrCreate(['name' => 'edit-users']);
        Permission::firstOrCreate(['name' => 'delete-users']);

        Role::firstOrCreate(['name' => 'SuperAdmin']);
        Role::firstOrCreate(['name' => 'User']);

        $adminRole = Role::firstOrCreate(['name' => 'Admin']);

        $adminRole->givePermissionTo([
            'create-users',
            'edit-users',
            'delete-users',
        ]);
    }
}
