<?php

namespace App\Jobs\ImportDataLama;

use App\Jobs\ImportDataLama\Chunk\ChunkHotspotDaily;
use App\Models\Provinsi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class HotspotDaily implements ShouldQueue
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
        DB::delete('truncate hotspot_daily');
        DB::connection('pgsql_hotspot')->table('hotspot_daily')->where('provinsi_id', '>', 0)->where('provinsi_id', '<=', 34)->whereNotNull('provinsi_id')->orderBy('id')->chunk(1000, function ($datas) use ($dataProvs) {
            dispatch((new ChunkHotspotDaily($datas, $dataProvs))->onQueue('migrasi'));
        });
    }

    public function tags()
    {
        return ['migrasi', 'migrasi:hotspot-daily', 'hotspot-daily:get-data'];
    }
}
