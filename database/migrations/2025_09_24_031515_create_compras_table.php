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
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->string('producto');          // nombre del producto comprado
            $table->integer('cantidad');         // cantidad seleccionada
            $table->decimal('precio', 8, 2);     // precio unitario
            $table->decimal('total', 8, 2);      // precio * cantidad
            $table->timestamps();                // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
