<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('reference')->unique();
            $table->string('status')->default('pending');
            $table->json('items');
            $table->timestamps();
        });

        Schema::create('order_store', function (Blueprint $table) {
            $table->foreignId('order_id')->constrained('user_orders')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->primary(['order_id', 'store_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_store');
        Schema::dropIfExists('user_orders');
    }
};
