<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_drop_old_compras_table.php
return new class extends Migration {
    public function up(){ Schema::dropIfExists('compras'); }
    public function down(){} // no necesitamos rollback
};