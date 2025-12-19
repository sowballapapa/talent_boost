<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\TransactionType;
use App\Models\TransactionStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('income_user')->constrained('users')->cascadeOnDelete();
            $table->foreignId('outcome_user')->constrained('users')->cascadeOnDelete();
            $table->foreignIdFor(TransactionType::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(TransactionStatus::class)->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('transaction_hash')->nullable();
            $table->string('offline_id')->nullable()->unique();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
