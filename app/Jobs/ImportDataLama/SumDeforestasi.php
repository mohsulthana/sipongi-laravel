<?php

namespace App\Jobs\ImportDataLama;

use App\Jobs\ImportDataLama\Chunk\ChunkSumDeforestasi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SumDeforestasi implements ShouldQueue
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
        DB::connection('pgsql_hotspot')->table('sum_deforestasi')->orderBy('id')->chunk(1000, function ($datas) {
            dispatch((new ChunkSumDeforestasi($datas))->onQueue('migrasi'));
        });
    }

    public function tags()
    {
        return ['migrasi', 'migrasi:sum-deforestasi', 'sum-deforestasi:get-data'];
    }
}
