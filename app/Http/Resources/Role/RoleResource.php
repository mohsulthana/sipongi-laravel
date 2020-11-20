<?php

namespace App\Http\Resources\Role;

use Illuminate\Http\Resources\Json\Resource;

class RoleResource extends Resource
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
            'permissions_count' => $this->permissions_count,
            'users_count' => $this->users_count,
            'users'         => new UserResources($this->users->sortBy('name')->take(4)->all()),
            'created_at'    => $this->created_at,
        ];

        return $res;
    }
}
