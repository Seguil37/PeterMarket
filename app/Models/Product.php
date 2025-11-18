<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    public function getImageUrlAttribute($value)
    {
        if (!$value) return null;
        if (str_starts_with($value, 'products/')) {
            return Storage::disk('public')->url($value);
        }
        return $value;
    }

    public function uploadedImagePath(): ?string
    {
        $raw = $this->getRawOriginal('image_url');
        return is_string($raw) && str_starts_with($raw, 'products/') ? $raw : null;
    }

    public function hasUploadedImage(): bool
    {
        return (bool) $this->uploadedImagePath();
    }

    public function applyInventory(string $type, int $qty): void
    {
        if ($type === 'in')  $this->increment('stock', $qty);
        if ($type === 'out') $this->decrement('stock', $qty);
    }
}
