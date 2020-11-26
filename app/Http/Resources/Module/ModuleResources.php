<?php

namespace App\Http\Resources\Module;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ModuleResources extends ResourceCollection
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
            'data' => ModuleResource::collection($this->collection),
        ];
    }
}
