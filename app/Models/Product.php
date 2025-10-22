<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class Product extends Model
{
    use HasFactory;

    // Campos que se pueden asignar en masa
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'image_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function applyInventory(string $type, int $qty): void
    {
        if ($type === 'in')  $this->increment('stock', $qty);
        if ($type === 'out') $this->decrement('stock', $qty);
    }
}
