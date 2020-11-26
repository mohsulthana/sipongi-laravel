<?php

namespace App\Http\Resources\LaporanHarian;

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
            'bulan'       => $this->bulan,
            'bulan_nama'  => $this->bulan_nama,
            'tahun'       => $this->tahun,
            'link'        => $this->link,
            'created_at'  => $this->created_at->format('Y-m-d H:i:s'),
        ];;
    }
}
