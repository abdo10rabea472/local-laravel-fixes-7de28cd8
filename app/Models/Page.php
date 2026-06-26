<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'content',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    public function getOgImageUrlAttribute(): ?string
    {
        if (! $this->og_image) {
            return null;
        }

        if (str_starts_with($this->og_image, 'http') || str_starts_with($this->og_image, '/')) {
            return $this->og_image;
        }

        if (str_starts_with($this->og_image, 'imges/') || str_starts_with($this->og_image, './imges/')) {
            return asset(ltrim($this->og_image, './'));
        }

        return asset('storage/' . $this->og_image);
    }
}
