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
        Schema::create('access_log', function (Blueprint $table) {
            $table->id();

            $table->string('ip_address')->nullable();
            $table->string('url')->nullable();
            $table->string('https')->nullable();
            $table->string('request_type')->nullable();
            $table->text('get_values')->nullable();
            $table->text('post_values')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_log');
    }
};
