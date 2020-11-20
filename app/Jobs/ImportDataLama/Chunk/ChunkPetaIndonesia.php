<?php

namespace App\Jobs\ImportDataLama\Chunk;

use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\KotaKab;
use App\Models\Provinsi;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChunkPetaIndonesia implements ShouldQueue
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
            $kelId = null;
            $namaKel = null;
            $kecId = null;
            $namaKec = null;
            $kabId = null;
            $namaKab = null;
            $provId = null;
            $namaProv = null;

            $checkKel = $this->checkKel($val->geom);
            if ($checkKel) {
                $kelId = $checkKel->id;
                $namaKel = $checkKel->nama;
                $kecId = $checkKel->kecamatan->id;
                $namaKec = $checkKel->kecamatan->nama;
                $kabId = $checkKel->kecamatan->kota_kab->id;
                $namaKab = $checkKel->kecamatan->kota_kab->nama;
                $provId = $checkKel->kecamatan->kota_kab->provinsi->id;
                $namaProv = $checkKel->kecamatan->kota_kab->provinsi->nama_provinsi;
            } else {
                $checkKec = $this->checkKec($val->geom);
                if ($checkKec) {
                    $kecId = $checkKec->id;
                    $namaKec = $checkKec->nama;
                    $kabId = $checkKec->kota_kab->id;
                    $namaKab = $checkKec->kota_kab->nama;
                    $provId = $checkKec->kota_kab->provinsi->id;
                    $namaProv = $checkKec->kota_kab->provinsi->nama_provinsi;
                } else {
                    $checkKab = $this->checkKab($val->geom);
                    if ($checkKab) {
                        $kabId = $checkKab->id;
                        $namaKab = $checkKab->nama;
                        $provId = $checkKab->provinsi->id;
                        $namaProv = $checkKab->provinsi->nama_provinsi;
                    } else {
                        $checkProv = $this->checkProv($val->geom);
                        $provId = $checkProv ? $checkProv->id : null;
                        $namaProv = $checkProv ? $checkProv->nama_provinsi : null;
                    }
                }
            }
            $data[] = array(
                'id' => Str::orderedUuid(),
                'placemark_id' => $val->placemark_id,
                'provinsi_id' => $provId,
                'provinsi' => $namaProv,
                'kotakab_id' => $kabId,
                'kabkota' => $namaKab,
                'kecamatan_id' => $kecId,
                'kecamatan' => $namaKec,
                'kelurahan_id' => $kelId,
                'desa' => $namaKel,
                'kawasan' => $val->kawasan,
                'nama_hti' => $val->nama_hti,
                'nama_ha' => $val->nama_ha,
                'nama_kebun' => $val->nama_kebun,
                'poligon' => $val->poligon,
                'geom' => $val->geom,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            );
        }

        DB::table('fungsi_kawasan')->insertOrIgnore($data);
        DB::delete(
            'DELETE FROM fungsi_kawasan WHERE placemark_id IN ( 
                SELECT placemark_id FROM ( 
                    SELECT placemark_id, row_number() OVER w as rnum FROM fungsi_kawasan 
                    WINDOW w AS ( 
                        PARTITION BY placemark_id,provinsi_id,kotakab_id,kecamatan_id,kelurahan_id,kawasan,nama_hti,nama_ha,nama_kebun,poligon ORDER BY id 
                    )
                ) t 
            WHERE t.rnum > 1)'
        );
        activity()->enableLogging();
    }

    public function tags()
    {
        return ['migrasi', 'migrasi:peta-indonesia', 'peta-indonesia:insert-data'];
    }

    public function checkKel($geom)
    {
        $checkKel = Kelurahan::query()
            ->select(['id', 'kecamatan_id', 'nama'])
            ->with([
                'kecamatan' => function ($q) {
                    $q->select(['id', 'kotakab_id', 'nama']);
                },
                'kecamatan.kota_kab' => function ($q) {
                    $q->select(['id', 'provinsi_id', 'nama']);
                },
                'kecamatan.kota_kab.provinsi' => function ($q) {
                    $q->select(['id', 'nama_provinsi']);
                }
            ])
            ->whereRaw(
                "ST_Contains(
                        geom,
                        ST_MakeValid(?)
                    )",
                [$geom]
            )
            ->first();

        return $checkKel;
    }

    public function checkKec($geom)
    {
        $checkKec = Kecamatan::query()
            ->select(['id', 'kotakab_id', 'nama'])
            ->with([
                'kota_kab' => function ($q) {
                    $q->select(['id', 'provinsi_id', 'nama']);
                },
                'kota_kab.provinsi' => function ($q) {
                    $q->select(['id', 'nama_provinsi']);
                }
            ])
            ->whereRaw(
                "ST_Contains(
                        geom,
                        ST_MakeValid(?)
                    )",
                [$geom]
            )
            ->first();

        return $checkKec;
    }

    public function checkKab($geom)
    {
        $checkKab = KotaKab::query()
            ->select(['id', 'provinsi_id', 'nama'])
            ->with([
                'provinsi' => function ($q) {
                    $q->select(['id', 'nama_provinsi']);
                }
            ])
            ->whereRaw(
                "ST_Contains(
                        geom,
                        ST_MakeValid(?)
                    )",
                [$geom]
            )
            ->first();

        return $checkKab;
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
            ->first();

        return $checkProv;
    }
}
