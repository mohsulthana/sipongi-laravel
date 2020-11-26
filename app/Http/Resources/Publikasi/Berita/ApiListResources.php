<?php

namespace App\Http\Resources\Publikasi\Berita;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ApiListResources extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => ApiListResource::collection($this->collection),
        ];
    }
}
