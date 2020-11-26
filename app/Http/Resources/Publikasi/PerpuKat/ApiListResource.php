<?php

namespace App\Http\Resources\Publikasi\PerpuKat;

use App\Http\Resources\Publikasi\Perpu\ApiListResources;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiListResource extends JsonResource
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
            'slug'          => $this->slug,
            'name'          => $this->name,
            'perpu'          => new ApiListResources($this->perpu)
        ];
    }
}
