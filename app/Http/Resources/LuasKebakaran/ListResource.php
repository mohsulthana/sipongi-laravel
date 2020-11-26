<?php

namespace App\Http\Resources\LuasKebakaran;

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
            'id' => (string) $this->id,
            'provinsi_id' => $this->provinsi_id,
            'provinsi' => (string) $this->Provinsi->nama_provinsi,
            'tahun' => (int) $this->tahun,
            'luas_kebakaran' => $this->luas_kebakaran
        ];
    }
}
