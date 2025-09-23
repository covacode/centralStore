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
        Schema::create('stock_sync_log', function (Blueprint $table) {
            $table->id();
            $table->string('event_uuid')->unique();
            $table->unsignedBigInteger('store')->nullable();
            $table->unsignedBigInteger('product')->nullable();
            $table->string('status'); // processed, ignored, not_found, error
            $table->json('payload');
            $table->text('message')->nullable();
            $table->timestamp('processed_at');
            $table->timestamps();
            $table->index(['event_uuid']);
            $table->index(['store', 'product']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_sync_log');
    }
};
