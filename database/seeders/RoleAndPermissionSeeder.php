<?php

namespace Database\Seeders;

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
    public function run(): void
    {
        Permission::firstOrCreate(['name' => 'users.read', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'users.create', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'users.update', 'guard_name' => 'api']);
        Permission::firstOrCreate(['name' => 'users.delete', 'guard_name' => 'api']);

        Role::firstOrCreate(['name' => 'SuperAdmin', 'guard_name' => 'api']);

        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'api']);
        $adminRole->givePermissionTo([
            'users.read',
        ]);

        Role::firstOrCreate(['name' => 'User', 'guard_name' => 'api']);
    }
}
