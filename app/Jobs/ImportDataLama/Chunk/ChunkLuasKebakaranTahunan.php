<?php

namespace App\Jobs\ImportDataLama\Chunk;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChunkLuasKebakaranTahunan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $datas;
    private $dataProvs;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($datas, $dataProvs)
    {
        $this->datas = $datas;
        $this->dataProvs = $dataProvs;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        activity()->disableLogging();
        $data = array();
        foreach ($this->datas as $val) {
            $checkProv = $this->dataProvs->where('old_id', $val->provinsi_id)->first();
            $provId = $checkProv ? $checkProv->id : null;

            $data[] = array(
                'id' => Str::orderedUuid(),
                'provinsi_id' => $provId,
                'tahun' => $val->tahun,
                'luas_kebakaran' => $val->luas_kebakaran,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            );
        }
        DB::table('luas_kebakaran_tahunan')->insertOrIgnore($data);
        activity()->enableLogging();
    }

    public function tags()
    {
        return ['migrasi', 'migrasi:luas-kebakaran-tahunan', 'luas-kebakaran-tahunan:insert-data'];
    }
}
