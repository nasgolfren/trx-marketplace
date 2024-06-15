<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders_sell', function (Blueprint $table) {
            $table->id();
            $table->uuid('unique_id');
            $table->foreignId('order_id')->constrained('orders');

            $table->string('payout_target_address');
            $table->unsignedBigInteger('amount');
            $table->double('reward', 15, 2)->nullable();
            $table->double('total_reward', 15, 2)->nullable();
            $table->string('txid')->nullable();
            $table->string('reward_txid')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_sell');
    }
};
