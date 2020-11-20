<?php

namespace App\Http\Resources\Daops;

use Illuminate\Http\Resources\Json\ResourceCollection;

class GeoListResources extends ResourceCollection
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
            'type' => 'FeatureCollection',
            'features' => GeoListResource::collection($this->collection),
        ];
    }
}
