<?php

namespace App\Http\Resources\KotaKab;

use Illuminate\Http\Resources\Json\JsonResource;
use MStaack\LaravelPostgis\Geometries\Point;

class ClusterKotaKabResource extends JsonResource
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
                        "nama" => $this->nama,
                        "provinsi" => $this->provinsi->nama_provinsi,
                        "pulau" => $this->provinsi->pulau
                    ],
                    'geometry' => $this->geom
                ]
            ],
            'center' => json_decode($this->centroid)->coordinates
        ];
    }
}
