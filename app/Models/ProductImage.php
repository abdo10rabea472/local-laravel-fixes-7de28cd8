<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'image',
        'thumb',
        'medium',
        'large',
        'sort_order',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getUrl(string $size = 'medium'): string
    {
        $path = match ($size) {
            'thumb' => $this->thumb,
            'large' => $this->large,
            default => $this->medium,
        };

        $path = $path ?: $this->image;

        if (str_starts_with($path, 'http') || str_starts_with($path, '/')) {
            return $path;
        }

        if (str_starts_with($path, 'imges/') || str_starts_with($path, './imges/')) {
            return asset(ltrim($path, './'));
        }

        return Storage::disk('public')->url($path);
    }
}
