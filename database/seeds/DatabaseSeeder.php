<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        activity()->disableLogging();
        $this->call(RolesSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(LuasAreaSeeder::class);
        $this->call(BeritaSeeder::class);
        $this->call(DokumenLainSeeder::class);
        $this->call(PerpuSeeder::class);
        $this->call(GaleriSeeder::class);

        if (Storage::exists("sql/regional.sql")) {
            $regional = Storage::get("sql/regional.sql");
            DB::unprepared($regional);
        }
        if (Storage::exists("sql/provinsi.sql")) {
            $provinsi = Storage::get("sql/provinsi.sql");
            DB::unprepared($provinsi);
        }
        if (Storage::exists("sql/kotakab.sql")) {
            $kotakab = Storage::get("sql/kotakab.sql");
            DB::unprepared($kotakab);
        }
        if (Storage::exists("sql/kecamatan.sql")) {
            $kecamatan = Storage::get("sql/kecamatan.sql");
            DB::unprepared($kecamatan);
        }
        if (Storage::exists("sql/kelurahan.sql")) {
            $kelurahan = Storage::get("sql/kelurahan.sql");
            DB::unprepared($kelurahan);
        }
        if (Storage::exists("sql/daops.sql")) {
            $daops = Storage::get("sql/daops.sql");
            DB::unprepared($daops);
        }
        activity()->enableLogging();
    }
}
