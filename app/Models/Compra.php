<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; // 👈 importa el trait

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $fillable = ['producto', 'cantidad', 'precio', 'total'];
}