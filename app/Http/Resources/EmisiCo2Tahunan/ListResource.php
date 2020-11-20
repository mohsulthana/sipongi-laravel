<?php

namespace App\Http\Resources\EmisiCo2Tahunan;

use Illuminate\Http\Resources\Json\JsonResource;

class ListResource extends JsonResource
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
            'id'          => (string) $this->id,
            'provinsi_id' => $this->provinsi_id,
            'provinsi'    => $this->Provinsi->nama_provinsi,
            'tahun'       => $this->tahun,
            'total'       => $this->total,
            'created_at'  => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
