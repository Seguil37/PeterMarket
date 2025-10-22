<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_create_inventory_movements_table.php
return new class extends Migration {
    public function up(){
        Schema::create('inventory_movements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->enum('type', ['in','out']); // in = entrada, out = salida
            $t->unsignedInteger('quantity'); // cantidad positiva
            $t->decimal('unit_cost', 10, 2)->nullable(); // opcional (para entradas)
            $t->text('note')->nullable(); // motivo / referencia
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // quien lo hizo
            $t->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('inventory_movements'); }
};
