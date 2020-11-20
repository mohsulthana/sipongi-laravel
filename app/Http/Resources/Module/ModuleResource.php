<?php

namespace App\Http\Resources\Module;

use Illuminate\Http\Resources\Json\Resource;

class ModuleResource extends Resource
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
            'name'          => $this->name,
            'display_name'  => $this->display_name,
            'permissions'   => $this->permissions
        ];
    }
}
