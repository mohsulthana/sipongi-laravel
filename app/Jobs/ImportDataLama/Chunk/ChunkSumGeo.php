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

class ChunkSumGeo implements ShouldQueue
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
            $checkProv = $this->dataProvs->where('id_provinsi_kms', $val->proid)->first();
            $provId = $checkProv ? $checkProv->id : null;
            $data[] = array(
                'id' => Str::orderedUuid(),
                'provinsi_id' => $provId,
                'provinsi' => $val->provinsi,
                'kabid' => $val->kabid,
                'kabkota' => $val->kabkota,
                'kecid' => $val->kecid,
                'kecamatan' => $val->kecamatan,
                'desid' => $val->desid,
                'desa' => $val->desa,
                'nama' => $val->nama,
                'jenis' => $val->jenis,
                'kategori' => $val->kategori,
                'geom' => $val->geom,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            );
        }

        DB::table('sum_geo')->insertOrIgnore($data);
        DB::delete(
            'DELETE FROM sum_geo WHERE id IN ( 
                SELECT id FROM ( 
                    SELECT id, row_number() OVER w as rnum FROM sum_geo 
                    WINDOW w AS ( 
                        PARTITION BY provinsi_id, provinsi, kabid, kecid, kecamatan, desid, desa, nama, jenis, kategori ORDER BY id 
                    )
                ) t 
            WHERE t.rnum > 1)'
        );
        activity()->enableLogging();
    }

    public function tags()
    {
        return ['migrasi', 'migrasi:sum-geo', 'sum-geo:insert-data'];
    }
}
