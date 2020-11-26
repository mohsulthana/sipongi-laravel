<?php

namespace App\Jobs\ImportDataLama;

use App\Jobs\ImportDataLama\Chunk\ChunkUsers;
use App\Models\Daops;
use App\Models\Provinsi;
use App\Models\Regional;
use App\Models\Spatie\Role;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class Users implements ShouldQueue
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
        $dataReg = Regional::query()->select(['id'])->get();
        $dataProv = Provinsi::query()->select(['id', 'old_id'])->get();
        $dataDaops = Daops::query()->select(['id', 'kode_daops'])->get();
        $dataRole = Role::query()->select(['id', 'name'])->get();
        DB::connection('pgsql_hotspot')->table('users')->orderBy('id')->chunk(1000, function ($datas) use ($dataReg, $dataProv, $dataDaops, $dataRole) {
            dispatch((new ChunkUsers($datas, $dataReg, $dataProv, $dataDaops, $dataRole))->onQueue('migrasi'));
        });
    }

    public function tags()
    {
        return ['migrasi', 'migrasi:users', 'users:get-data'];
    }
}
