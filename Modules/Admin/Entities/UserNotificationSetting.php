<?php
// modules/Finance/Entities/UserNotificationSetting.php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Entities\User;

class UserNotificationSetting extends Model
{
    use HasFactory;

    protected $table = 'user_notification_settings';
    protected $guarded = [];

    protected $casts = [
        'email_enabled' => 'boolean',
        'push_enabled' => 'boolean',
        'in_app_enabled' => 'boolean',
        'channels' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isEnabledFor(string $channel): bool
    {
        return match($channel) {
            'email' => $this->email_enabled,
            'push' => $this->push_enabled,
            'in_app' => $this->in_app_enabled,
            default => false,
        };
    }

    public function getEnabledChannels(): array
    {
        $channels = [];

        if ($this->email_enabled) $channels[] = 'email';
        if ($this->push_enabled) $channels[] = 'push';
        if ($this->in_app_enabled) $channels[] = 'in_app';

        return $channels;
    }

    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\UserNotificationSettingFactory::new();
    }
}
