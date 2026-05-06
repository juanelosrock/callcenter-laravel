<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'ver pedidos',
            'crear pedidos',
            'ver menu',
            'ver reportes',
            'gestionar usuarios',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($permissions);

        $supervisor = Role::firstOrCreate(['name' => 'supervisor']);
        $supervisor->syncPermissions(['ver pedidos', 'crear pedidos', 'ver menu', 'ver reportes']);

        $agente = Role::firstOrCreate(['name' => 'agente']);
        $agente->syncPermissions(['ver pedidos', 'crear pedidos', 'ver menu']);
    }
}
