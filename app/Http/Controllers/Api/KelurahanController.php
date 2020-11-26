<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Handler as Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Kelurahan\ClusterDesaResource;
use App\Models\Kelurahan;
use Illuminate\Http\Request;

class KelurahanController extends Controller
{
    public function getClusterDesa($desaId)
    {
        $query = Kelurahan::query();
        $query->with([
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
        $query->where('id', $desaId);
        $data = $query->first();
        ClusterDesaResource::withoutWrapping();

        return new ClusterDesaResource($data);
    }
}
