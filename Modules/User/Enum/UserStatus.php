<?php
// modules/User/Enum/UserStatus.php

namespace Modules\User\Enum;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
}