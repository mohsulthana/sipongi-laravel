<?php

namespace App\Http\Resources\Tematic;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ListResource extends JsonResource
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
            'id'            => (string) $this->id,
            'title'          => $this->title,
            'image_url'          => $this->image_url,
	    'publish'          => (bool) $this->publish,
            'created_at'          => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}

