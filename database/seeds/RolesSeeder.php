<?php

use App\Models\Module;
use App\Models\Spatie\Permission;
use App\Models\Spatie\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Module
        $module = Module::firstOrCreate(
            [
                'name' => 'user_roles'
            ],
            [
                'name' => 'user_roles',
                'display_name' => 'Hak Akses',
                'icon' => 'icon-key'
            ]
        );

        // Permissions
        $permissions = [
            [
                'name' => 'read-roles',
                'display_name' => 'Akses',
                'guard_name' => config('auth.defaults.guard'),
                'order' => 1,
                'module_id' => $module->id
            ],
            [
                'name' => 'create-roles',
                'display_name' => 'Tambah',
                'guard_name' => config('auth.defaults.guard'),
                'order' => 2,
                'module_id' => $module->id
            ],
            [
                'name' => 'update-roles',
                'display_name' => 'Ubah',
                'guard_name' => config('auth.defaults.guard'),
                'order' => 3,
                'module_id' => $module->id
            ],
            [
                'name' => 'delete-roles',
                'display_name' => 'Hapus',
                'guard_name' => config('auth.defaults.guard'),
                'order' => 4,
                'module_id' => $module->id
            ]
        ];

        foreach ($permissions as $key => $value) {
            Permission::firstOrCreate([
                'name' => $value['name'],
                'module_id' => $module->id
            ], $value);
        }

        $roles = [
            [
                'name' => 'pengguna',
                'display_name' => 'Pengguna',
                'guard_name' => config('auth.defaults.guard')
            ],
            [
                'name' => 'operator',
                'display_name' => 'Operator',
                'guard_name' => config('auth.defaults.guard')
            ]
        ];

        foreach ($roles as $key => $value) {
            Role::firstOrCreate(['name' => $value['name']], $value);
        }
    }
}
