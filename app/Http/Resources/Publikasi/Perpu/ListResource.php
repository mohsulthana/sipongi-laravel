<?php

namespace App\Http\Resources\Publikasi\Perpu;

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
            'id'            => (string) $this->id,
            'kategori_id'          => $this->kategori_id,
            'nomor'          => $this->nomor,
            'title'          => $this->title,
            'tipe'          => $this->tipe,
            'file'          => $this->file,
            'file_url'          => $this->file_url,
            'kategori'          => $this->kategori,
            'created_at'          => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
