<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',

            'view projects',
            'create projects',
            'edit projects',
            'delete projects',

            'create tasks',
            'edit tasks',
            'delete tasks',


            'view permissions',

            'view roles',
            'create roles',
            'edit roles',
            'delete roles',

            'view users',
            'create users',
            'edit users',
            'delete users',

            'view activities',

            'view invoices',
            'create invoices',
            'edit invoices',
            'delete invoices',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $projectManager = Role::firstOrCreate(['name' => 'project_manager']);
        $teamMember = Role::firstOrCreate(['name' => 'team_member']);
        $sales = Role::firstOrCreate(['name' => 'sales']);

        $admin->syncPermissions(Permission::all());
    }
}
