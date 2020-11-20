<?php

namespace App\Http\Resources\Publikasi\DokumenLain;

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
            'title'          => $this->title,
            'private'          => (bool) $this->private,
            'tipe'          => $this->tipe,
            'file'          => $this->file,
            'file_url'          => $this->file_url,
            'created_at'          => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
