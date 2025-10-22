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
        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('customer_name');
            $t->string('customer_email');
            $t->string('customer_address')->nullable();
            $t->string('payment_method'); // 'simulated' | 'cash' | 'card'
            $t->string('status')->default('paid'); // 'paid','pending','failed'
            $t->decimal('subtotal',10,2);
            $t->decimal('tax',10,2);
            $t->decimal('total',10,2);
            $t->string('payment_ref')->nullable();
            $t->timestamps();
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
