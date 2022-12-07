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
        Permission::firstOrCreate(['name' => 'create-users', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'edit-users', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'delete-users', 'guard_name' => 'api']);

        Role::firstOrCreate(['name' => 'SuperAdmin', 'guard_name' => 'api']);

        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'api']);
        $adminRole->givePermissionTo([
            'create-users',
            'edit-users',
            'delete-users',
        ]);

        Role::firstOrCreate(['name' => 'User', 'guard_name' => 'api']);
    }
}
