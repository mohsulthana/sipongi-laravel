<?php

namespace App\Jobs\ImportDataLama;

use App\Jobs\ImportDataLama\Chunk\ChunkRawGeoKabKota;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RawGeoKabKota implements ShouldQueue
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
        $dataProvs = DB::connection('pgsql_hotspot')->table('provinsi')->select(['id', 'id_provinsi_kms'])->get();
        DB::connection('pgsql_hotspot')->table('raw_geo_kabkota')->orderBy('id')->chunk(1000, function ($datas) use ($dataProvs) {
            dispatch((new ChunkRawGeoKabKota($datas, $dataProvs))->onQueue('migrasi'));
        });
    }

    public function tags()
    {
        return ['migrasi', 'migrasi:raw-geo-kabkota', 'raw-geo-kabkota:get-data'];
    }
}
