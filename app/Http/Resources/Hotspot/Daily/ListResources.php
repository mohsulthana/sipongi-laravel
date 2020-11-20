<?php

namespace App\Http\Resources\Hotspot\Daily;

use Illuminate\Http\Resources\Json\JsonResource;

class ListResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
