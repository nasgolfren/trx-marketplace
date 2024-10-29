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
            $table->boolean('is_multisignature')->default(false);
            $table->string('multisignature_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_sell', function (Blueprint $table) {
            $table->dropColumn('is_multisignature');
            $table->dropColumn('multisignature_address');
        });
    }
};
