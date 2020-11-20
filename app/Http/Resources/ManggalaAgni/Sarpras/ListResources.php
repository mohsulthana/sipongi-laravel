<?php

namespace App\Http\Resources\ManggalaAgni\Sarpras;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ListResources extends ResourceCollection
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
            'data' => ListResource::collection($this->collection),
        ];
    }
}
