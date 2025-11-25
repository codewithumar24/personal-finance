<?php
// modules/Finance/Database/Migrations/2024_01_01_000007_create_budgets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->uuid('budget_uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->json('notifications')->nullable(); // Track which notifications sent
            $table->timestamps();

            $table->index('budget_uuid');
            $table->index('user_id');
            $table->index('category_id');
            $table->index(['start_date', 'end_date']);
            $table->index('is_active');
            
            $table->unique(['user_id', 'category_id', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};