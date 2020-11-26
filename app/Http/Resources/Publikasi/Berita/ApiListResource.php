<?php

namespace App\Http\Resources\Publikasi\Berita;

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
            'image_url'          => $this->image_url,
            'created_at'          => $this->created_at->isoFormat('DD MMMM Y')
        ];
    }
}
