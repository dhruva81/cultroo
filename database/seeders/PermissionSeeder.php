<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            'content_writer',
            'editor',
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate([
                'name' => $role
            ], [
                'guard_name' => 'web'
            ]);
        }

        //   Permissions for admin team
        $permissions = [
            'can_update_status',
            'can_update_access'
        ];

        // Insert in database
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                [
                    'name' => $permission
                ],
                [
                    'guard_name' => 'web',
                ]
            );
        }

        $role = Role::findByName('editor');
        $role->givePermissionTo('can_update_status');
        $role->givePermissionTo('can_update_access');


//        $role->givePermissionTo('edit articles');

        // Sync with database
        // $permissionsInDB = Permission::all();
        // $permissionsInSeeder = collect($permissions)
        //     ->collapse()
        //     ->map(fn ($per) => $per['name']);

        // foreach ($permissionsInDB as $permission) {
        //     if (!$permissionsInSeeder->contains($permission->name)) {
        //         $permission->delete();
        //     }
        // }
    }
}
