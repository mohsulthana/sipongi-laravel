<?php

namespace App\Jobs\ImportDataLama;

use App\Jobs\ImportDataLama\Chunk\ChunkLuasKebakaranTahunan;
use App\Models\Provinsi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class LuasKebakaranTahunan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $dataProvs = Provinsi::query()->select(['id', 'old_id'])->get();
        DB::delete('truncate luas_kebakaran_tahunan');
        DB::connection('pgsql_hotspot')->table('luas_kebakaran_tahunan')->orderBy('provinsi_id')->orderBy('tahun')->chunk(1000, function ($datas) use ($dataProvs) {
            dispatch((new ChunkLuasKebakaranTahunan($datas, $dataProvs))->onQueue('migrasi'));
        });
    }

    public function tags()
    {
        return ['migrasi', 'migrasi:luas-kebakaran-tahunan', 'luas-kebakaran-tahunan:get-data'];
    }
}
