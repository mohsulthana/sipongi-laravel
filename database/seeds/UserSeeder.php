<?php

use App\Models\Module;
use App\Models\Spatie\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
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
                'name' => 'users'
            ],
            [
                'name' => 'users',
                'display_name' => 'Pengguna',
                'icon' => 'icon-key'
            ]
        );

        // Permissions
        $permissions = [
            [
                'name' => 'read-users',
                'display_name' => 'Akses',
                'guard_name' => config('auth.defaults.guard'),
                'order' => 1,
                'module_id' => $module->id
            ],
            [
                'name' => 'create-users',
                'display_name' => 'Tambah',
                'guard_name' => config('auth.defaults.guard'),
                'order' => 2,
                'module_id' => $module->id
            ],
            [
                'name' => 'update-users',
                'display_name' => 'Ubah',
                'guard_name' => config('auth.defaults.guard'),
                'order' => 3,
                'module_id' => $module->id
            ],
            [
                'name' => 'delete-users',
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

        // Create default user
        $user = User::firstOrCreate(
            [
                'username' => 'SysAdminSipongi'
            ],
            [
                'username' => 'sysAdminSipongi',
                'name' => 'Super Admin',
                'is_super_admin' => true,
                'password' => Hash::make('AdminSipongi1!')
            ]
        );
    }
}
