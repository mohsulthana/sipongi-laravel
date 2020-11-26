<?php

namespace App\Http\Resources\LuasKebakaran;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EntriLuasKebakaranReseources extends ResourceCollection
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
            'data' => EntriLuasKebakaranReseource::collection($this->collection),
        ];
    }
}
