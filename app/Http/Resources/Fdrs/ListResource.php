<?php

namespace App\Http\Resources\Fdrs;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

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
            'id'            => (string) $this->id,
            'wilayah'       => $this->Wilayah->nama,
            'index'         => $this->Index->nama,
            'hari'          => $this->Hari->nama,
            'date'          => Carbon::parse($this->date)->format('d F Y'),
            'image'         => $this->image,
            'image_url'     => $this->image_url,
            'created_at'    => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
