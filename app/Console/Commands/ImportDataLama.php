<?php

namespace App\Console\Commands;

use App\Jobs\ImportDataLama\HotspotDaily;
use App\Jobs\ImportDataLama\HotspotSatelit;
use App\Jobs\ImportDataLama\LuasKebakaranTahunan;
use App\Jobs\ImportDataLama\PetaIndonesia;
use App\Jobs\ImportDataLama\PetaKawasan;
use App\Jobs\ImportDataLama\Settings;
use App\Jobs\ImportDataLama\SumDeforestasi;
use App\Jobs\ImportDataLama\SumEnergi;
use App\Jobs\ImportDataLama\SumGambut;
use App\Jobs\ImportDataLama\SumKawasanHutan;
use App\Jobs\ImportDataLama\SumKonsesi;
use App\Jobs\ImportDataLama\SumLhnKritis;
use App\Jobs\ImportDataLama\SumLhnTutupan;
use App\Jobs\ImportDataLama\SumPipib;
use App\Jobs\ImportDataLama\SumPipib5;
use App\Jobs\ImportDataLama\SumPipib6;
use App\Jobs\ImportDataLama\SumWilayahAdat;
use App\Jobs\ImportDataLama\Users;
use Illuminate\Console\Command;

class ImportDataLama extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrasi:data-lama';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ambil Data Lama';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Users::withChain([
            new Settings(),
            new SumDeforestasi(),
            new SumEnergi(),
            new SumGambut(),
            new SumKawasanHutan(),
            new SumKonsesi(),
            new SumLhnKritis(),
            new SumLhnTutupan(),
            new SumPipib(),
            new SumPipib5(),
            new SumPipib6(),
            new SumWilayahAdat(),
            new LuasKebakaranTahunan(),
            new PetaKawasan(),
            new PetaIndonesia(),
            new HotspotDaily(),
            new HotspotSatelit(),
        ])->dispatch()->allOnQueue('migrasi');

        // dispatch((new PetaKawasan())->onQueue('migrasi'));
    }
}
