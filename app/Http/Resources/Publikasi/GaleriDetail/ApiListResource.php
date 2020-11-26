<?php

namespace App\Http\Resources\Publikasi\GaleriDetail;

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
            'keterangan'          => $this->keterangan,
            'image_url'          => $this->image_url,
            'created_at'          => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
