<?php

use App\Models\Module;
use App\Models\Publikasi\PerpuKategori;
use App\Models\Spatie\Permission;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PerpuSeeder extends Seeder
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
                'name' => 'perpu'
            ],
            [
                'name' => 'perpu',
                'display_name' => 'Peraturan Perundangan',
                'icon' => 'icon-key'
            ]
        );

        // Permissions
        $permissions = [
            [
                'name' => 'read-perpu',
                'display_name' => 'Akses',
                'guard_name' => config('auth.defaults.guard'),
                'order' => 1,
                'module_id' => $module->id
            ],
            [
                'name' => 'create-perpu',
                'display_name' => 'Tambah',
                'guard_name' => config('auth.defaults.guard'),
                'order' => 2,
                'module_id' => $module->id
            ],
            [
                'name' => 'update-perpu',
                'display_name' => 'Ubah',
                'guard_name' => config('auth.defaults.guard'),
                'order' => 3,
                'module_id' => $module->id
            ],
            [
                'name' => 'delete-perpu',
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

        // kategori
        $kategories = [
            [
                'name' => 'PERATURAN MENTERI LINGKUNGAN HIDUP DAN KEHUTANAN'
            ],
            [
                'name' => 'UNDANG-UNDANG'
            ],
            [
                'name' => 'Peraturan Pemerintah'
            ],
            [
                'name' => 'Keputusan Presiden'
            ],
            [
                'name' => 'SK Menteri Lingkungan Hidup'
            ],
            [
                'name' => 'SK Menteri Kehutanan'
            ],
            [
                'name' => 'SK Dirjen PHPA/PHKA'
            ],
            [
                'name' => 'Peraturan Daerah'
            ],
            [
                'name' => 'Peraturan Gubernur'
            ],
            [
                'name' => 'SK Gubernur Kepala Daerah Tingkat I'
            ],
            [
                'name' => 'Lain-lain',
                'created_at' => Carbon::now()->subYears(1)
            ],
        ];

        foreach ($kategories as $key => $value) {
            PerpuKategori::firstOrCreate([
                'name' => $value['name']
            ], $value);
        }
    }
}
