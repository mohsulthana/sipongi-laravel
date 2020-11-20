<?php

use App\Models\Module;
use App\Models\Spatie\Permission;
use Illuminate\Database\Seeder;

class LuasAreaSeeder extends Seeder
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
                'name' => 'entri_luas_area_kebakaran'
            ],
            [
                'name' => 'entri_luas_area_kebakaran',
                'display_name' => 'Entri Luas Area Kebakaran',
                'icon' => 'icon-key'
            ]
        );

        // Permissions
        $permissions = [
            [
                'name' => 'read-entri_luas_area_kebakaran',
                'display_name' => 'Akses',
                'guard_name' => config('auth.defaults.guard'),
                'order' => 1,
                'module_id' => $module->id
            ],
            [
                'name' => 'update-entri_luas_area_kebakaran',
                'display_name' => 'Ubah',
                'guard_name' => config('auth.defaults.guard'),
                'order' => 3,
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
