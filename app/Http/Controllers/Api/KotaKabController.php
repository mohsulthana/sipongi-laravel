<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Handler as Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\KotaKab\ClusterKotaKabResource;
use App\Http\Resources\Prov\ClusterProvResource;
use App\Models\KotaKab;
use App\Models\Provinsi;
use App\Models\SumGambut;
use App\Models\SumPipib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KotaKabController extends Controller
{
    public function getByProv($provId)
    {
        $query = Provinsi::query();
        $query->select([
            'id',
            'nama_provinsi',
            'pulau',
            //'geom',
            DB::raw("ST_AsGeoJSON(ST_Centroid(geom)) as centroid"),
        ]);
        $query->with([
            'kota_kab' => function ($q) {
                $q->select(['id', 'provinsi_id', 'nama']);
            }
        ]);
        $query->where('id', $provId);
        $data = $query->first();

        ClusterProvResource::withoutWrapping();

        return new ClusterProvResource($data);
    }

    public function getClusterKotaKab($kotaKabId)
    {
        $query = KotaKab::query();
        $query->select([
            '*',
            DB::raw("ST_AsGeoJSON(ST_Centroid(geom)) as centroid"),
        ]);
        $query->with([
            'provinsi' => function ($q) {
                $q->select(['id', 'nama_provinsi', 'pulau']);
            }
        ]);
        $query->where('id', $kotaKabId);
        $data = $query->first();
        ClusterKotaKabResource::withoutWrapping();

        return new ClusterKotaKabResource($data);
    }

    public function getPetaGambut($provId)
    {
        $gambut = SumGambut::query();
        $gambut->select([
            'kabkota',
            'provinsi_id',
            'jenis',
            'sumber',
            'nama',
            'kml',
            'geom',
            DB::raw("ST_AsGeoJSON(ST_Centroid(geom)) as centroid"),
        ]);
        $gambut->with([
            'provinsi' => function ($q) {
                $q->select([
                    'id',
                    'nama_provinsi',
                    'pulau',
                    \DB::raw("ST_AsGeoJSON(ST_Centroid(geom)) as centroid")
                ]);
            }
        ]);
        $gambut->where('provinsi_id', $provId);
        $datas = $gambut->get();

        $response = [];
        foreach ($datas as $key => $data) {
            $response[] = $this->setDataPetaGambut($data);
        }

        $prov = Provinsi::select([
            '*',
            DB::raw("ST_AsGeoJSON(ST_Centroid(geom)) as centroid")
        ])
            ->where('id', $provId)
            ->first();

        return [
            'prov' => [
                'center' => json_decode($prov->centroid)->coordinates,
            ],
            'kabkota' => $response
        ];
    }

    public function setDataPetaGambut($gambut)
    {
        return [
            'type' => 'Feature',
            "properties" => [
                "nama" => $gambut->kabkota,
                "provinsi" => $gambut->provinsi->nama_provinsi,
                "pulau" => $gambut->provinsi->pulau,
                "kedalaman" => $gambut->jenis,
                "sumber" => $gambut->sumber,
                "lahan" => $gambut->nama,
                "kml" => $gambut->klm,
            ],
            'geometry' => $gambut->geom
        ];
    }

    public function getPetaMoratorium($provId)
    {
        $Moratorium = SumPipib::query();
        $Moratorium->select([
            'kabkota',
            'provinsi_id',
            'jenis',
            'sumber',
            'nama',
            'kml',
            'geom',
            DB::raw("ST_AsGeoJSON(ST_Centroid(geom)) as centroid"),
        ]);
        $Moratorium->with([
            'provinsi' => function ($q) {
                $q->select([
                    'id',
                    'nama_provinsi',
                    'pulau',
                    \DB::raw("ST_AsGeoJSON(ST_Centroid(geom)) as centroid")
                ]);
            }
        ]);
        $Moratorium->where('provinsi_id', $provId);
        $datas = $Moratorium->get();

        $response = [];
        foreach ($datas as $key => $data) {
            $response[] = $this->setDataPetaMoratorium($data);
        }

        $prov = Provinsi::select([
            '*',
            DB::raw("ST_AsGeoJSON(ST_Centroid(geom)) as centroid")
        ])
            ->where('id', $provId)
            ->first();

        return [
            'prov' => [
                'center' => json_decode($prov->centroid)->coordinates,
            ],
            'kabkota' => $response
        ];
    }

    public function setDataPetaMoratorium($Moratorium)
    {
        return [
            'type' => 'Feature',
            "properties" => [
                "nama" => $Moratorium->kabkota,
                "provinsi" => $Moratorium->provinsi->nama_provinsi,
                "pulau" => $Moratorium->provinsi->pulau,
                "jenis" => $Moratorium->jenis,
                "sumber" => $Moratorium->sumber,
                "lahan" => $Moratorium->nama,
                "kml" => $Moratorium->klm,
            ],
            'geometry' => $Moratorium->geom
        ];
    }
}
