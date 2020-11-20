<?php

namespace App\Http\Resources\Kelurahan;

use Illuminate\Http\Resources\Json\JsonResource;

class ClusterDesaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    "properties" => [
                        "desa" => $this->nama,
                        "kec" => $this->kecamatan->nama,
                        "kabkota" => $this->kecamatan->kota_kab->nama,
                        "provinsi" => $this->kecamatan->kota_kab->provinsi->nama_provinsi,
                        "pulau" => $this->kecamatan->kota_kab->provinsi->pulau,
                    ],
                    'geometry' => $this->geom
                ]
            ]
        ];
    }
}
