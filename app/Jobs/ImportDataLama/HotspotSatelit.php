<?php

namespace App\Jobs\ImportDataLama;

use App\Jobs\ImportDataLama\Chunk\ChunkHotspotSatelit;
use App\Jobs\ImportDataLama\Chunk\ChunkHotspotSatelitDeleted;
use App\Models\Provinsi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class HotspotSatelit implements ShouldQueue
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
        DB::delete('truncate hotspot_satelit');
        DB::connection('pgsql_hotspot')->table('hotspot_satelit')->orderBy('date_hotspot')->orderBy('x')->orderBy('y')->orderBy('sumber')->chunk(1000, function ($datas) {
            dispatch((new ChunkHotspotSatelit($datas))->onQueue('migrasi'));
        });
        DB::connection('pgsql_hotspot')->table('hotspot_satelit_removed')->orderBy('date_hotspot')->orderBy('x')->orderBy('y')->orderBy('sumber')->chunk(1000, function ($datas) {
            dispatch((new ChunkHotspotSatelitDeleted($datas))->onQueue('migrasi'));
        });
    }

    public function tags()
    {
        return ['migrasi', 'migrasi:hotspot-satelit', 'hotspot-satelit:get-data'];
    }
}
