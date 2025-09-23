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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store')->constrained('stores')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('product')->constrained('products')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('available_quantity')->nullable()->default(0);
            $table->integer('reserved_quantity')->nullable()->default(0);
            $table->integer('total_quantity')->nullable()->default(0); // sum of available and reserved
            $table->integer('sold_quantity')->nullable()->default(0);
            $table->unique(['store', 'product']);
            $table->timestamps();
            $table->unsignedBigInteger('stock_version')->default(0);
            $table->timestamp('stock_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
