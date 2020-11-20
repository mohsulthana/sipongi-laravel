<?php

use Illuminate\Database\Seeder;
use App\Models\Fdrs\OptionIndex;
use App\Http\Controllers\Api\Fdrs\BmkgController;

class FdrsIndexTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $wilayah = new BmkgController();

        foreach ($wilayah->optionIndex() as $key => $value) {
            OptionIndex::create([
                'key' => $key,
                'nama' => $value,
            ]);
        }
    }
}
