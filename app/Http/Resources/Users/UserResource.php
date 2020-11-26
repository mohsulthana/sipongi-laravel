<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'username' => $this->username,
            'name' => $this->name,
            'email' => $this->email,
            'is_super_admin' => (bool) $this->is_super_admin,
            'status' => (bool) $this->status,
            'default_pass' => (bool) $this->default_pass,
            'created_at' => $this->created_at,
            'role_name' => $this->role_name,
            'role_name_id' => $this->role_name_id,
            'avatar_url' => $this->avatar_url,
            'roles_ids' => $this->getRoleNames(),
            'permissions' => $this->getAllPermissions()->pluck('name')
        ];
    }
}
