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
            $table->uuid('uuid');
            $table->foreignUuid('user_uuid')->nullable()->constrained('users', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('order_status_uuid')->nullable()->constrained('order_statuses', 'uuid')->nullOnDelete();
            $table->foreignUuid('payment_uuid')->nullable()->constrained('payments', 'uuid')->nullOnDelete();
            $table->json('products');
            $table->json('address');
            $table->decimal('delivery_fee')->nullable();
            $table->decimal('amount');
            $table->timestamps();
            $table->timestamp('shipped_at')->nullable();
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
