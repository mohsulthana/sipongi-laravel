<?php

namespace App\Http\Resources\IndoHotspot;

use App\Traits\HotspotSatelitTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ListResource extends JsonResource
{
    use HotspotSatelitTrait;
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
            'geometry' => $this->geom,
            'properties' => [
                'lat' => (float) $this->y,
                'long' => (float) $this->x,
                'sumber' => $this->sumber,
                'ori_sumber' => $this->hotspot_name($this->sumber2, true),
                'date_hotspot' => $this->date_hotspot->isoFormat('dddd, DD MMMM YYYY'),
                'desa_id' => $this->kelurahan_id,
                'counter' => $this->counter,
                'confidence' => $this->confidence,
                'confidence_level' => $this->confidence_level,
                'kawasan' => Str::title($this->kawasan),
                'desa' => Str::title($this->desa),
                'kecamatan' => Str::title($this->kecamatan),
                'kabkota' => Str::title($this->kabkota),
                'nama_provinsi' => $this->provinsi ? Str::title($this->provinsi_rel->nama_provinsi) : null,
                'pulau' => $this->provinsi_rel ? Str::title($this->provinsi_rel->pulau) : null,
            ],
        ];
    }
}
