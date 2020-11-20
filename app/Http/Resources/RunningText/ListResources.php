<?php

namespace App\Http\Resources\RunningText;

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
