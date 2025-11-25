<?php
// modules/Finance/Database/Migrations/2024_01_01_000005_create_categories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('category_uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['income', 'expense', 'transfer', 'savings']);
            $table->string('color', 7)->default('#6B7280');
            $table->string('icon')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('category_uuid');
            $table->index('user_id');
            $table->index('type');
            $table->index('is_default');
            $table->index(['user_id', 'type', 'is_active']);
            
            $table->unique(['user_id', 'name', 'type']);
        });

        // Insert default categories
        $this->seedDefaultCategories();
    }

    private function seedDefaultCategories(): void
    {
        $defaultCategories = [
            // Income Categories
            ['name' => 'Salary', 'type' => 'income', 'color' => '#10B981', 'icon' => '💵', 'is_default' => true],
            ['name' => 'Bonus', 'type' => 'income', 'color' => '#10B981', 'icon' => '🎁', 'is_default' => true],
            ['name' => 'Investment', 'type' => 'income', 'color' => '#10B981', 'icon' => '📈', 'is_default' => true],
            ['name' => 'Freelance', 'type' => 'income', 'color' => '#10B981', 'icon' => '💼', 'is_default' => true],
            ['name' => 'Business', 'type' => 'income', 'color' => '#10B981', 'icon' => '🏢', 'is_default' => true],

            // Expense Categories
            ['name' => 'Food & Dining', 'type' => 'expense', 'color' => '#EF4444', 'icon' => '🍕', 'is_default' => true],
            ['name' => 'Transportation', 'type' => 'expense', 'color' => '#EF4444', 'icon' => '🚗', 'is_default' => true],
            ['name' => 'Shopping', 'type' => 'expense', 'color' => '#EF4444', 'icon' => '🛍️', 'is_default' => true],
            ['name' => 'Bills & Utilities', 'type' => 'expense', 'color' => '#EF4444', 'icon' => '💡', 'is_default' => true],
            ['name' => 'Entertainment', 'type' => 'expense', 'color' => '#EF4444', 'icon' => '🎬', 'is_default' => true],
            ['name' => 'Healthcare', 'type' => 'expense', 'color' => '#EF4444', 'icon' => '🏥', 'is_default' => true],
            ['name' => 'Education', 'type' => 'expense', 'color' => '#EF4444', 'icon' => '📚', 'is_default' => true],
            ['name' => 'Travel', 'type' => 'expense', 'color' => '#EF4444', 'icon' => '✈️', 'is_default' => true],

            // Savings Categories
            ['name' => 'Emergency Fund', 'type' => 'savings', 'color' => '#3B82F6', 'icon' => '🛡️', 'is_default' => true],
            ['name' => 'Retirement', 'type' => 'savings', 'color' => '#3B82F6', 'icon' => '👵', 'is_default' => true],
            ['name' => 'Investment', 'type' => 'savings', 'color' => '#3B82F6', 'icon' => '💰', 'is_default' => true],

            // Transfer Category
            ['name' => 'Transfer', 'type' => 'transfer', 'color' => '#8B5CF6', 'icon' => '🔄', 'is_default' => true],
        ];

        foreach ($defaultCategories as $category) {
            DB::table('categories')->insert(array_merge($category, [
                'category_uuid' => Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};