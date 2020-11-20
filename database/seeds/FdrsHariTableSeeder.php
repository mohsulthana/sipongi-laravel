<?php

use Illuminate\Database\Seeder;
use App\Models\Fdrs\OptionHari;
use App\Http\Controllers\Api\Fdrs\BmkgController;

class FdrsHariTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $wilayah = new BmkgController();

        foreach ($wilayah->optionHari() as $key => $value) {
            OptionHari::create([
                'key' => $key,
                'nama' => $value,
            ]);
        }
    }
}
