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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('unique_id');

            $table->unsignedBigInteger('amount');
            $table->unsignedInteger('hours');
            $table->unsignedBigInteger('price');
            $table->string('source_address');
            $table->string('target_address');
            $table->string('resource');

            $table->boolean('partial_fill');
            $table->boolean('multisignature');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
