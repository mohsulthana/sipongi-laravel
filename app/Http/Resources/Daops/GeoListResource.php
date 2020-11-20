<?php

namespace App\Http\Resources\Daops;

use Illuminate\Http\Resources\Json\JsonResource;
use MStaack\LaravelPostgis\Geometries\Point;

class GeoListResource extends JsonResource
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
            'type' => 'Feature',
            'geometry' => new Point($this->latitude, $this->longitude),
            'properties' => [
                'kode_daops' => (string) $this->kode_daops,
                'nama_daops' => (string) $this->nama_daops,
                'alamat' => (string) $this->alamat,
                'telepon' => (string) $this->telepon,
            ],
        ];
    }
}
