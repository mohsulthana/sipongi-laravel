<?php

namespace App\Jobs\ImportDataLama;

use App\Jobs\ImportDataLama\Chunk\ChunkPetaIndonesia;
use App\Models\Provinsi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class PetaIndonesia implements ShouldQueue
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
        DB::connection('pgsql_hotspot')->table('peta_indonesia')->where('provinsi_id', '>', 0)->where('provinsi_id', '<=', 34)->orderBy('placemark_id')->chunk(1000, function ($datas) {
            dispatch((new ChunkPetaIndonesia($datas))->onQueue('migrasi'));
        });
    }

    public function tags()
    {
        return ['migrasi', 'migrasi:peta-indonesia', 'peta-indonesia:get-data'];
    }
}
