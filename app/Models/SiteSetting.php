<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $settings = Cache::rememberForever('site_settings', function () {
            return self::all()->keyBy('key');
        });

        return $settings->get($key)?->value ?? $default;
    }

    public static function getUrl(string $key, ?string $default = null): ?string
    {
        $value = self::get($key);

        if (! $value) {
            return $default;
        }

        if (str_starts_with($value, 'http') || str_starts_with($value, '/')) {
            return $value;
        }

        if (str_starts_with($value, 'imges/') || str_starts_with($value, './imges/')) {
            return asset(ltrim($value, './'));
        }

        return asset('storage/' . $value);
    }

    public static function clearCache(): void
    {
        Cache::forget('site_settings');
    }

    protected static function booted(): void
    {
        static::saved(fn () => self::clearCache());
        static::deleted(fn () => self::clearCache());
    }
}
