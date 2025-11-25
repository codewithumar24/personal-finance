<?php
// modules/Core/Database/Migrations/2024_01_01_000001_create_roles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->uuid('role_uuid')->unique();
            $table->string('name')->unique();
            $table->string('guard_name')->default('api');
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index('role_uuid');
            $table->index('name');
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('permission_uuid')->unique();
            $table->string('name')->unique();
            $table->string('guard_name')->default('api');
            $table->string('group')->default('system');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('permission_uuid');
            $table->index('name');
            $table->index('group');
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            
            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');
                
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};