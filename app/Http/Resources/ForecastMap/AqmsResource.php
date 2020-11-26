<?php

namespace App\Http\Resources\ForecastMap;

use Illuminate\Http\Resources\Json\JsonResource;
use MStaack\LaravelPostgis\Geometries\Point;

class AqmsResource extends JsonResource
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
            'type' => 'Feature',
            'geometry' => new Point($this->latitude, $this->longitude),
            'properties' => $this->resource
        ];
    }
}
