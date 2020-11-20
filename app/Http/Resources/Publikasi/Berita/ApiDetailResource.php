<?php

namespace App\Http\Resources\Publikasi\Berita;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiDetailResource extends JsonResource
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
            'desc'          => $this->desc,
            'meta_desc' => str_limit(strip_tags($this->desc), 155, '...'),
            'image_url'          => $this->image_url,
            'publish_at'          => $this->created_at->isoFormat('DD MMMM Y'),
            'created_at'          => $this->created_at
        ];
    }
}
