<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSection extends Model
{
    public const TYPES = [
        'banner',
        'slider',
        'featured_products',
        'latest_products',
        'gallery',
        'faq',
        'video',
        'html_block',
        'text_block',
    ];

    protected $fillable = [
        'category_id',
        'section_type',
        'title',
        'content',
        'image',
        'background_image',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'content' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
