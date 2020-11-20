<?php

namespace App\Jobs\ImportDataLama\Chunk;

use App\Models\Provinsi;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChunkPetaKawasan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $datas;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($datas)
    {
        $this->datas = $datas;
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
            $provs = $this->checkProv($val->geom);
            if ($provs->count() > 0) {
                foreach ($provs as $prov) {
                    $data[] = array(
                        'id' => Str::orderedUuid(),
                        'placemark_id' => $val->placemark_id,
                        'placemark_name' => $val->placemark_name,
                        'fungsi' => $val->fungsi,
                        'fungsi_kawasan' => $val->fungsi_kawasan,
                        'sk_kawasan' => $val->sk_kawasan,
                        'tgl_kawasan' => $val->tgl_kawasan,
                        'luas_kawasan' => $val->luas_kawasan,
                        'dpcls' => $val->dpcls,
                        'keterangan' => $val->keterangan,
                        'provinsi_id' => $prov->id,
                        'provinsi' => $prov->nama_provinsi,
                        'shape_leng' => $val->shape_leng,
                        'shape_area' => $val->shape_area,
                        'poligon' => $val->poligon,
                        'geom' => $val->geom,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    );
                }
            } else {
                $data[] = array(
                    'id' => Str::orderedUuid(),
                    'placemark_id' => $val->placemark_id,
                    'placemark_name' => $val->placemark_name,
                    'fungsi' => $val->fungsi,
                    'fungsi_kawasan' => $val->fungsi_kawasan,
                    'sk_kawasan' => $val->sk_kawasan,
                    'tgl_kawasan' => $val->tgl_kawasan,
                    'luas_kawasan' => $val->luas_kawasan,
                    'dpcls' => $val->dpcls,
                    'keterangan' => $val->keterangan,
                    'provinsi_id' => null,
                    'provinsi' => null,
                    'shape_leng' => $val->shape_leng,
                    'shape_area' => $val->shape_area,
                    'poligon' => $val->poligon,
                    'geom' => $val->geom,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                );
            }
        }

        DB::table('peta_kawasan')->insertOrIgnore($data);
        DB::delete(
            'DELETE FROM peta_kawasan WHERE placemark_id IN ( 
                SELECT placemark_id FROM ( 
                    SELECT placemark_id, row_number() OVER w as rnum FROM peta_kawasan 
                    WINDOW w AS ( 
                        PARTITION BY placemark_id, placemark_name, fungsi, fungsi_kawasan, sk_kawasan, tgl_kawasan, luas_kawasan, dpcls, keterangan, provinsi_id,shape_leng,shape_area,poligon  ORDER BY id 
                    )
                ) t 
            WHERE t.rnum > 1)'
        );
        activity()->enableLogging();
    }

    public function tags()
    {
        return ['migrasi', 'migrasi:peta-kawasan', 'peta-kawasan:insert-data'];
    }

    public function checkProv($geom)
    {
        $checkProv = Provinsi::query()
            ->select(['id', 'nama_provinsi'])
            ->whereRaw(
                "ST_Intersects(
                        geom,
                        ST_MakeValid(?)
                    )",
                [$geom]
            )
            ->get();

        return $checkProv;
    }
}
