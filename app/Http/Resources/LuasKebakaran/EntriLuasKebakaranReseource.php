<?php

namespace App\Http\Resources\LuasKebakaran;

use Illuminate\Http\Resources\Json\JsonResource;

class EntriLuasKebakaranReseource extends JsonResource
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
            'id' => (string) $this->id,
            'nama' => (string) $this->nama,
            'luas' => $this->luas ? (float) $this->luas : (float) 0.000
        ];
    }
}
