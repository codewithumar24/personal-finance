<?php
// modules/Finance/Transformers/CategoryTransformer.php

namespace Modules\Admin\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryTransformer extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->category_uuid,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'type' => $this->type->value,
            'type_label' => $this->type_label,
            'color' => $this->color,
            'icon' => $this->icon,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'description' => $this->description,
            'transaction_count' => $this->whenLoaded('transactions', function () {
                return $this->transactions->count();
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}