<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Currency extends Model
{
    protected $fillable = [
        'name', 'code', 'symbol', 'symbol_position', 'decimals',
        'decimal_separator', 'thousands_separator', 'exchange_rate',
        'is_default', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'decimals'      => 'integer',
        'exchange_rate' => 'decimal:8',
        'is_default'    => 'boolean',
        'is_active'     => 'boolean',
        'sort_order'    => 'integer',
    ];

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }

    public function format(float $amount): string
    {
        $formatted = number_format(
            $amount,
            $this->decimals,
            $this->decimal_separator,
            $this->thousands_separator
        );
        return $this->symbol_position === 'before'
            ? "{$this->symbol}{$formatted}"
            : "{$formatted} {$this->symbol}";
    }

    protected static function booted(): void
    {
        static::saved(function (self $cur) {
            if ($cur->is_default) {
                static::where('id', '!=', $cur->id)->update(['is_default' => false]);
            }
            \Illuminate\Support\Facades\Cache::forget('currencies:all');
            \Illuminate\Support\Facades\Cache::forget('currencies:default');
        });
        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('currencies:all');
            \Illuminate\Support\Facades\Cache::forget('currencies:default');
        });
    }
}
