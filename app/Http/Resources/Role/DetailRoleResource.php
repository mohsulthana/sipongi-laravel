<?php

namespace App\Http\Resources\Role;

use Illuminate\Http\Resources\Json\Resource;

class DetailRoleResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $res = [
            'id'            => (string) $this->id,
            'name'          => $this->name,
            'display_name'  => $this->display_name,
            'permission_ids' => $this->permissions->pluck('id'),
            'created_at'    => $this->created_at,
        ];

        return $res;
    }
}
