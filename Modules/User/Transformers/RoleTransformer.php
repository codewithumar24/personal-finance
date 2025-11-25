<?php
// modules/User/Transformers/RoleTransformer.php

namespace Modules\User\Transformers;

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
        ];
    }
}