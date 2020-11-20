<?php

namespace App\Jobs\ImportDataLama\Chunk;

use App\Models\Kelurahan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChunkHotspotSatelit implements ShouldQueue
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
            $x = (float) $val->x;
            $y = (float) $val->y;
            $geom = $val->geom;
            $temp = null;

            try {
                DB::select("select ST_Transform(ST_SetSRID(ST_Point($x,$y), 4326),3857) as distance");
            } catch (\Exception $e) {
                $temp = $x;
                $x = $y;
                $y = $temp;
                $temp = null;
                $geom = DB::raw("ST_GeomFromText('POINT(' || $x || ' ' || $y || ')',4326)");
            }

            $kelId = null;
            $namaKel = null;
            $kecId = null;
            $namaKec = null;
            $kabId = null;
            $namaKab = null;
            $provId = null;
            $namaProv = 'LUAR INDONESIA';

            $checkKel1 = $this->checkKel1($x, $y);
            if ($checkKel1) {
                $kelId = $checkKel1->id;
                $namaKel = $checkKel1->nama;
                $kecId = $checkKel1->kecamatan->id;
                $namaKec = $checkKel1->kecamatan->nama;
                $kabId = $checkKel1->kecamatan->kota_kab->id;
                $namaKab = $checkKel1->kecamatan->kota_kab->nama;
                $provId = $checkKel1->kecamatan->kota_kab->provinsi->id;
                $namaProv = $checkKel1->kecamatan->kota_kab->provinsi->nama_provinsi;
            } else {
                $checkKel2 = $this->checkKel1($x, $y);
                if ($checkKel2) {
                    $kelId = $checkKel2->id;
                    $namaKel = $checkKel2->nama;
                    $kecId = $checkKel2->kecamatan->id;
                    $namaKec = $checkKel2->kecamatan->nama;
                    $kabId = $checkKel2->kecamatan->kota_kab->id;
                    $namaKab = $checkKel2->kecamatan->kota_kab->nama;
                    $provId = $checkKel2->kecamatan->kota_kab->provinsi->id;
                    $namaProv = $checkKel2->kecamatan->kota_kab->provinsi->nama_provinsi;
                }
            }

            $data[] = array(
                'id' => Str::orderedUuid(),
                'date_hotspot' => $val->date_hotspot,
                'x' => $x,
                'y' => $y,
                'sumber' => $val->sumber,
                'source' => $val->source,
                'confidence' => $val->confidence ? $val->confidence : null,
                'brightness' => $val->brightness,
                'provinsi_id' => $provId,
                'provinsi' => $namaProv,
                'kotakab_id' => $kabId,
                'kabkota' => $namaKab,
                'kecamatan_id' => $kecId,
                'kecamatan' => $namaKec,
                'kelurahan_id' => $kelId,
                'desa' => $namaKel,
                'kawasan' => $val->kawasan,
                'counter' => $val->counter,
                'fungsi_kawasan' => $val->fungsi_kawasan,
                'sk_kawasan' => $val->sk_kawasan,
                'nama_hti' => $val->nama_hti,
                'nama_ha' => $val->nama_ha,
                'nama_kebun' => $val->nama_kebun,
                'sumber2' => $val->sumber2,
                'geom' => $geom,
                'hotspot_daily_update' => true,
                'created_at' => $val->date_inserted,
                'updated_at' => $val->date_updated
            );
        }
        DB::table('hotspot_satelit')->insertOrIgnore($data);
        activity()->enableLogging();
    }

    public function tags()
    {
        return ['migrasi', 'migrasi:hotspot-satelit', 'hotspot-satelit:insert-data'];
    }

    public function checkKel1($x, $y)
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
                        ST_MakeValid(ST_SetSRID(ST_Point(?,?), 4326))
                    )",
                [$x, $y]
            )
            ->first();

        return $checkKel;
    }

    public function checkKel2($x, $y)
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
                "ST_Intersects(
                        geom,
                        ST_MakeValid(ST_SetSRID(ST_Point(?,?), 4326))
                    )",
                [$x, $y]
            )
            ->first();

        return $checkKel;
    }
}
