<?php

namespace App\Console\Commands;

use App\Jobs\ReadHotspot\HotspotLapan;
use App\Jobs\ReadHotspot\HotspotModis;
use App\Models\Settings;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Console\Command;

class ReadHotspot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'read:hotspot 
                                {--all : Proses semua hotspot}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get data hotspot';

    protected $tags;

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
        $proses = array();
        if (!$this->option('all')) {
            $proses = $this->choice(
                'pilih proses?',
                ['NASA-MODIS', 'LAPAN'],
                null,
                null,
                true
            );
        }

        $setting = Settings::query()->where('account', 'HOTSPOT')->first();
        if ($setting->running) {
            if (in_array('NASA-MODIS', $proses) || $this->option('all')) {
                dispatch((new HotspotModis($setting))->onQueue('hotspot'));
            }

            if (in_array('LAPAN', $proses) || $this->option('all')) {
                try {
                    $date = Carbon::now();
                    if (!$this->option('all')) {
                        $date = Carbon::createFromFormat('Ymd', $this->ask('Isi tanggal data lapan YYYYMMDD?'));
                    }
                    dispatch((new HotspotLapan($setting, $date))->onQueue('hotspot'));
                } catch (InvalidFormatException $exp) {
                    return $this->error('Format tanggal harus YYYYMMDD!');
                }
            }
        }
    }
}
