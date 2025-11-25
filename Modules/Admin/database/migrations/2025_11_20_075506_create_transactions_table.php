<?php
// modules/Finance/Database/Migrations/2024_01_01_000006_create_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('transaction_uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_wallet_id')->nullable()->constrained('wallets')->onDelete('cascade');
            $table->foreignId('to_wallet_id')->nullable()->constrained('wallets')->onDelete('cascade');
            
            $table->enum('type', ['income', 'expense', 'transfer']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->date('transaction_date');
            $table->string('attachment')->nullable();
            $table->string('reference_number')->nullable();
            $table->json('tags')->nullable();
            
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurring_frequency', ['daily', 'weekly', 'monthly', 'yearly'])->nullable();
            $table->date('recurring_end_date')->nullable();
            $table->foreignId('parent_transaction_id')->nullable()->constrained('transactions')->onDelete('cascade');
            
            $table->timestamps();

            $table->index('transaction_uuid');
            $table->index('user_id');
            $table->index('wallet_id');
            $table->index('category_id');
            $table->index('type');
            $table->index('transaction_date');
            $table->index(['user_id', 'transaction_date']);
            $table->index(['wallet_id', 'transaction_date']);
            $table->index(['type', 'transaction_date']);
            $table->index('is_recurring');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};