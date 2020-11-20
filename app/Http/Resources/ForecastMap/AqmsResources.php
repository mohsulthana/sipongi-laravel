<?php

namespace App\Http\Resources\ForecastMap;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AqmsResources extends ResourceCollection
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
            'features' => AqmsResource::collection($this->collection),
        ];
    }
}
