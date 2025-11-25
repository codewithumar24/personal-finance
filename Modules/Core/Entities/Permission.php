<?php
// modules/Core/Entities/Permission.php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'permissions';
    protected $guarded = [];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions');
    }

    protected static function newFactory()
    {
        return \Modules\Core\Database\factories\PermissionFactory::new();
    }
}