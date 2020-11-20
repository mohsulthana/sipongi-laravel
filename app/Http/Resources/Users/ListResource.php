<?php

namespace App\Http\Resources\Users;

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
            'id' => (string) $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'name' => $this->name,
            'avatar_url' => $this->avatar_url,
            'is_super_admin' => (bool) $this->is_super_admin,
            'status' => (bool) $this->status,
            'created_at' => $this->created_at,
            'deleted_at' => $this->deleted_at,
            'role_name' => $this->role_name,
            'role_name_id' => $this->role_name_id,
            'regional_id' => $this->regional_id,
            'regional_name' => $this->regional ? $this->regional->nama_regional : null,
            'provinsi_id' => $this->provinsi_id,
            'provinsi_name' => $this->provinsi ? $this->provinsi->nama_provinsi : null,
            'unit_kerja' => $this->unit_kerja,
            'keterangan' => $this->keterangan,
        ];
    }
}
