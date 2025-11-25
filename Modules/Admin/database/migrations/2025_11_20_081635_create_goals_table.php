<?php
// modules/Finance/Database/Migrations/2024_01_01_000008_create_goals_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->uuid('goal_uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('target_amount', 15, 2);
            $table->decimal('current_amount', 15, 2)->default(0);
            $table->date('target_date');
            $table->enum('type', ['savings', 'debt', 'investment', 'purchase']);
            $table->string('color', 7)->default('#3B82F6');
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->dateTime('completed_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('goal_uuid');
            $table->index('user_id');
            $table->index('type');
            $table->index('is_completed');
            $table->index('is_active');
            $table->index('target_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};