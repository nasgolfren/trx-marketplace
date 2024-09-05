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
        Schema::table('orders_sell', function (Blueprint $table) {
            $table->double('delegated_amount_sun', 15, 2)->nullable();
            $table->double('delegated_amount_trx', 15, 2)->nullable();

            $table->dropColumn('reward_txid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_sell', function (Blueprint $table) {
            //
        });
    }
};
