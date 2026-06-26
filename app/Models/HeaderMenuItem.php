<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HeaderMenuItem extends Model
{
    protected $fillable = [
        'parent_id',
        'title',
        'url',
        'type',
        'coupon_code',
        'coupon_percent',
        'icon',
        'target',
        'position',
        'status',
        'location',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'position' => 'integer',
            'coupon_percent' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(HeaderMenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(HeaderMenuItem::class, 'parent_id')->orderBy('position');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeByLocation($query, string $location)
    {
        return $query->where('location', $location);
    }

    public function getUrlAttribute($value): ?string
    {
        if (! $value) {
            return '#';
        }

        if (str_starts_with($value, 'http') || str_starts_with($value, '/') || str_starts_with($value, '#')) {
            return $value;
        }

        return url($value);
    }
}
