<?php

namespace App\Http\Resources\ManggalaAgni\Sarpras;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'date'          => $this->date,
            'text'          => $this->text,
            'active'        => $this->active,
            'created_at'    => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
