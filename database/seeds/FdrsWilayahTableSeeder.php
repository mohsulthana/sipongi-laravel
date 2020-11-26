<?php

use Illuminate\Database\Seeder;
use App\Models\Fdrs\OptionWilayah;
use App\Http\Controllers\Api\Fdrs\BmkgController;

class FdrsWilayahTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $wilayah = new BmkgController();

        foreach ($wilayah->optionWilayah() as $key => $value) {
            OptionWilayah::create([
                'key' => $key,
                'nama' => $value,
            ]);
        }
    }
}
