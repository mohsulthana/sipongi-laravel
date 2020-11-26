<?php

namespace App\Traits;

use App\Models\FungsiKawasan;
use App\Models\HotspotSatelit;
use App\Models\Kelurahan;
use App\Models\PetaKawasan;
use Illuminate\Support\Facades\DB;

trait ImportHotspot
{
    public function counterData($x, $y, $date, $setting, $sumber)
    {
        $checkData = HotspotSatelit::query()
            ->selectRaw(
                "ST_Distance(
                    ST_Transform(ST_SetSRID(ST_Point(x,y), 4326),3857),
                    ST_Transform(ST_SetSRID(ST_Point(?,?), 4326),3857))/2000 as distance",
                [$x, $y]
            )
            ->addSelect('counter')
            ->whereDate('date_hotspot', $date->subDays(1)->format('Y-m-d'))
            ->where('sumber', $sumber)
            ->whereRaw(
                "ST_DWithin(
                    ST_Transform(ST_SetSRID(ST_Point(x,y), 4326),3857),
                    ST_Transform(ST_SetSRID(ST_Point(?,?), 4326),3857),
                    ? * 1000
                )",
                [$x, $y, 2]
            )
            ->orderByRaw("ST_Transform(ST_SetSRID(ST_Point(x,y), 4326),3857) <-> ST_Transform(ST_SetSRID(ST_Point(?,?), 4326),3857)", [$x, $y])
            ->first();

        return $checkData;
    }

    public function fungsiKawasan($x, $y)
    {
        $fungsiKawasan = FungsiKawasan::query()
            ->select([
                'kawasan',
                'nama_hti',
                'nama_ha',
                'nama_kebun',
            ])
            ->whereRaw(
                "ST_Contains(
                    geom,
                    ST_SetSRID(ST_Point(?,?), 4326)
                )",
                [$x, $y]
            )
            ->first();

        return $fungsiKawasan;
    }

    public function petaKawasan($x, $y)
    {
        $petaKawasan = PetaKawasan::query()
            ->select([
                'fungsi',
                'sk_kawasan',
            ])
            ->whereRaw(
                "ST_Contains(
                    geom,
                    ST_SetSRID(ST_Point(?,?), 4326)
                )",
                [$x, $y]
            )
            ->first();

        return $petaKawasan;
    }

    public function checkKel($x, $y, $ntersects = false)
    {
        $query = Kelurahan::query()
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
            ]);

        $query->when($ntersects, function ($query) use ($x, $y) {
            return $query->whereRaw(
                "ST_Intersects(
                    geom,
                    ST_SetSRID(ST_Point(?,?), 4326)
                )",
                [$x, $y]
            );
        });

        $query->when(!$ntersects, function ($query) use ($x, $y) {
            return $query->whereRaw(
                "ST_Contains(
                    geom,
                    ST_SetSRID(ST_Point(?,?), 4326)
                )",
                [$x, $y]
            );
        });

        $checkKel = $query->first();

        return $checkKel;
    }
}
