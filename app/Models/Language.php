<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Language extends Model
{
    protected $fillable = [
        'name', 'native_name', 'code', 'locale', 'direction',
        'flag', 'is_default', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }

    public function isRtl(): bool
    {
        return $this->direction === 'rtl';
    }

    protected static function booted(): void
    {
        static::saved(function (self $lang) {
            if ($lang->is_default) {
                static::where('id', '!=', $lang->id)->update(['is_default' => false]);
            }
            \Illuminate\Support\Facades\Cache::forget('languages:all');
            \Illuminate\Support\Facades\Cache::forget('languages:default');
        });
        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('languages:all');
            \Illuminate\Support\Facades\Cache::forget('languages:default');
        });
    }
}
