<?php
// modules/Core/Transformers/RoleTransformer.php

namespace Modules\Core\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleTransformer extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->role_uuid,
            'name' => $this->name,
            'description' => $this->description,
            'is_default' => $this->is_default,
            'permissions' => PermissionTransformer::collection($this->whenLoaded('permissions')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}