<?php

namespace App\Http\Resources\Settings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'firstName'     => $this->first_name,
            'lastName'      => $this->last_name,
            'email'         => $this->email,
            'username'      => $this->username,
            'active'        => $this->active,
            'lastLogin'     => $this->last_login_at,
            'company'       => new CompanyUserResource($this->whenLoaded('company')),
            'roles'         => RoleResource::collection($this->whenLoaded('roles')),
            'permissions'   => PermissionResource::collection($this->all_permissions),
        ];
    }
}
