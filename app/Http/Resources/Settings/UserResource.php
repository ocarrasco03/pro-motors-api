<?php

namespace App\Http\Resources\Settings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fullName' => $this->full_name,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'email' => $this->email,
            'username' => $this->username,
            'active' => $this->active,
            'lastLoginAt' => $this->last_login_at,
            'updatedBy' => $this->updated_by,
            'updatedAt' => $this->updated_at,
            'company' => new CompanyUserResource($this->whenLoaded('company')),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'permissions' => PermissionResource::collection($this->all_permissions),
        ];
    }
}
