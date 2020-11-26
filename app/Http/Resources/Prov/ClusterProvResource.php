<?php

namespace App\Http\Resources\Prov;

use Illuminate\Http\Resources\Json\JsonResource;
use MStaack\LaravelPostgis\Geometries\Point;

class ClusterProvResource extends JsonResource
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
            'prov' => [
                'type' => 'FeatureCollection',
                'features' => [
                    [
                        'type' => 'Feature',
                        "properties" => [
                            "nama" => $this->nama_provinsi,
                            "provinsi" => $this->nama_provinsi,
                            "pulau" => $this->pulau
                        ],
                        'geometry' => $this->geom
                    ]
                ],
                'center' => json_decode($this->centroid)->coordinates,  
            ],
            'kotakab' => $this->kota_kab
        ];
    }
}