<?php

namespace App\Http\Resources\Publikasi\DokumenLain;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiListResource extends JsonResource
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
            'slug'          => $this->slug,
            'title'          => $this->title,
            'tipe'          => $this->check_tipe,
            'file_url'          => $this->file_url,
            'created_at'          => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
