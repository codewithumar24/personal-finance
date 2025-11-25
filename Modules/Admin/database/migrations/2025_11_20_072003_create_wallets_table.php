<?php
// modules/Finance/Database/Migrations/2024_01_01_000004_create_wallets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->uuid('wallet_uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['cash', 'bank', 'digital_wallet', 'credit_card', 'savings']);
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('color', 7)->default('#3B82F6');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('wallet_uuid');
            $table->index('user_id');
            $table->index('type');
            $table->index('is_default');
            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};