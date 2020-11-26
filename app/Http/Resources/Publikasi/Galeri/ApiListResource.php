<?php

namespace App\Http\Resources\Publikasi\Galeri;

use App\Http\Resources\Publikasi\GaleriDetail\ApiListResource as AppApiListResource;
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
            'detail'          => new AppApiListResource($this->detail),
            'created_at'          => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
