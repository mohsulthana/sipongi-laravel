<?php

use App\Models\Module;
use App\Models\Spatie\Permission;
use Illuminate\Database\Seeder;

class GaleriSeeder extends Seeder
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
                'name' => 'galeri'
            ],
            [
                'name' => 'galeri',
                'display_name' => 'Galeri',
                'icon' => 'icon-key'
            ]
        );

        // Permissions
        $permissions = [
            [
                'name' => 'read-galeri',
                'display_name' => 'Akses',
                'guard_name' => config('auth.defaults.guard'),
                'order' => 1,
                'module_id' => $module->id
            ],
            [
                'name' => 'create-galeri',
                'display_name' => 'Tambah',
                'guard_name' => config('auth.defaults.guard'),
                'order' => 2,
                'module_id' => $module->id
            ],
            [
                'name' => 'update-galeri',
                'display_name' => 'Ubah',
                'guard_name' => config('auth.defaults.guard'),
                'order' => 3,
                'module_id' => $module->id
            ],
            [
                'name' => 'delete-galeri',
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
    }
}
