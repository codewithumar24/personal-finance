<?php
// modules/Core/Entities/Role.php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Entities\Permission;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $guarded = [];
    
    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }

    protected static function newFactory()
    {
        return \Modules\Core\Database\factories\RoleFactory::new();
    }
}