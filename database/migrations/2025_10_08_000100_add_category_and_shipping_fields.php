<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega campos para tipo de categoría, datos de envío y costo de delivery.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('category_type')->default('General')->after('name');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_address')->default('')->after('customer_email');
            $table->string('shipping_city')->default('')->after('shipping_address');
            $table->string('shipping_reference')->default('')->after('shipping_city');
            $table->string('shipping_type')->default('standard')->after('status');
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('tax');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('category_type')->default('General')->after('product_id');
        });
    }

    /**
     * Revierte los cambios.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('category_type');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_address','shipping_city','shipping_reference','shipping_type','shipping_cost']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('category_type');
        });
    }
};
