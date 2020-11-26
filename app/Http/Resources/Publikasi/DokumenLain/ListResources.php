<?php

namespace App\Http\Resources\Publikasi\DokumenLain;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ListResources extends ResourceCollection
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
            'data' => ListResource::collection($this->collection),
        ];
    }
}
