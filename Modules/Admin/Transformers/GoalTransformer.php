<?php
// modules/Finance/Transformers/GoalTransformer.php

namespace Modules\Admin\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GoalTransformer extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->goal_uuid,
            'name' => $this->name,
            'target_amount' => (float) $this->target_amount,
            'current_amount' => (float) $this->current_amount,
            'target_date' => $this->target_date,
            'type' => $this->type->value,
            'type_label' => $this->type_label,
            'color' => $this->color,
            'icon' => $this->icon,
            'description' => $this->description,
            'is_completed' => $this->is_completed,
            'completed_at' => $this->completed_at,
            'is_active' => $this->is_active,

            // Calculated fields
            'progress_percentage' => $this->progress_percentage,
            'days_remaining' => $this->days_remaining,
            'formatted_target_amount' => $this->formatted_target_amount,
            'formatted_current_amount' => $this->formatted_current_amount,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
