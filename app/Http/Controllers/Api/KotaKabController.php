<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Handler as Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\KotaKab\ClusterKotaKabResource;
use App\Models\KotaKab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KotaKabController extends Controller
{
    public function getByProv($provId)
    {
        $prov = KotaKab::query()
            ->select(['id', 'nama'])
            ->where('provinsi_id', $provId)
            ->orderBy('nama')
            ->get();

        return response()->json($prov);
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
}
