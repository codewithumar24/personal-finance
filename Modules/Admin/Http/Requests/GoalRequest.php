<?php
// modules/Finance/Http/Requests/GoalRequest.php

namespace Modules\Admin\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;
use Modules\Admin\DataTransfer\Requests\GoalDTO;
use Modules\Admin\Enum\GoalType;

class GoalRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'target_date' => ['required', 'date', 'after:today'],
            'type' => ['required', 'in:' . implode(',', GoalType::toArray())],
            'color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'icon' => ['nullable', 'string', 'max:10'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Goal name is required',
            'target_amount.required' => 'Target amount is required',
            'target_date.required' => 'Target date is required',
            'target_date.after' => 'Target date must be in the future',
            'type.required' => 'Goal type is required',
            'color.regex' => 'Color must be a valid hex color code',
        ];
    }

    public function getDTO(): GoalDTO
    {
        return GoalDTO::create(
            $this->input('name'),
            (float) $this->input('target_amount'),
            $this->input('target_date'),
            GoalType::from($this->input('type')),
            $this->input('color', '#3B82F6'),
            $this->input('icon'),
            $this->input('description'),
            $this->boolean('is_active', true)
        );
    }
}
