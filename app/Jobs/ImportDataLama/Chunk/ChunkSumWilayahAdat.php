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

class ChunkSumWilayahAdat implements ShouldQueue
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

            $checkKel = $this->checkKel($val->kml);
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
                $checkKec = $this->checkKec($val->kml);
                if ($checkKec) {
                    $kecId = $checkKec->id;
                    $namaKec = $checkKec->nama;
                    $kabId = $checkKec->kota_kab->id;
                    $namaKab = $checkKec->kota_kab->nama;
                    $provId = $checkKec->kota_kab->provinsi->id;
                    $namaProv = $checkKec->kota_kab->provinsi->nama_provinsi;
                } else {
                    $checkKab = $this->checkKab($val->kml);
                    if ($checkKab) {
                        $kabId = $checkKab->id;
                        $namaKab = $checkKab->nama;
                        $provId = $checkKab->provinsi->id;
                        $namaProv = $checkKab->provinsi->nama_provinsi;
                    } else {
                        $checkProv = $this->checkProv($val->kml);
                        $provId = $checkProv ? $checkProv->id : null;
                        $namaProv = $checkProv ? $checkProv->nama_provinsi : null;
                    }
                }
            }

            $data[] = array(
                'id' => Str::orderedUuid(),
                'provinsi_id' => $provId,
                'provinsi' => $namaProv,
                'kotakab_id' => $kabId,
                'kabkota' => $namaKab,
                'kecamatan_id' => $kecId,
                'kecamatan' => $namaKec,
                'kelurahan_id' => $kelId,
                'desa' => $namaKel,
                'nama' => $val->nama,
                'jenis' => $val->jenis,
                'kategori' => $val->kategori,
                'meta' => $val->meta,
                'kml' => $val->kml,
                'geom' => DB::raw("ST_SetSRID(ST_Multi(ST_geomFromKML('$val->kml')),4326)"),
                'sumber' => $val->sumber,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            );
        }
        DB::table('sum_wilayah_adat')->insertOrIgnore($data);
        DB::delete(
            'DELETE FROM sum_wilayah_adat WHERE id IN ( 
                SELECT id FROM ( 
                    SELECT id, row_number() OVER w as rnum FROM sum_wilayah_adat 
                    WINDOW w AS ( 
                        PARTITION BY provinsi_id, provinsi, kotakab_id, kecamatan_id, kecamatan, kelurahan_id, desa, nama, jenis, kategori, kml, sumber ORDER BY id 
                    )
                ) t 
            WHERE t.rnum > 1)'
        );
        activity()->enableLogging();
    }

    public function tags()
    {
        return ['migrasi', 'migrasi:sum-wilayah-adat', 'sum-wilayah-adat:insert-data'];
    }

    public function checkKel($kml)
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
                        ST_MakeValid(ST_SetSRID(ST_Multi(ST_geomFromKML(?)),4326))
                    )",
                [$kml]
            )
            ->first();

        return $checkKel;
    }

    public function checkKec($kml)
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
                        ST_MakeValid(ST_SetSRID(ST_Multi(ST_geomFromKML(?)),4326))
                    )",
                [$kml]
            )
            ->first();

        return $checkKec;
    }

    public function checkKab($kml)
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
                        ST_MakeValid(ST_SetSRID(ST_Multi(ST_geomFromKML(?)),4326))
                    )",
                [$kml]
            )
            ->first();

        return $checkKab;
    }

    public function checkProv($kml)
    {
        $checkProv = Provinsi::query()
            ->select(['id', 'nama_provinsi'])
            ->whereRaw(
                "ST_Intersects(
                        geom,
                        ST_MakeValid(ST_SetSRID(ST_Multi(ST_geomFromKML(?)),4326))
                    )",
                [$kml]
            )
            ->first();

        return $checkProv;
    }
}
