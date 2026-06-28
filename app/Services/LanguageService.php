<?php

namespace App\Services;

use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class LanguageService
{
    /** @return Collection<int, Language> */
    public function all(): Collection
    {
        return Cache::rememberForever('languages:all', function () {
            return Language::query()->active()->ordered()->get();
        });
    }

    public function default(): ?Language
    {
        return Cache::rememberForever('languages:default', function () {
            return Language::query()->active()->where('is_default', true)->first()
                ?? Language::query()->active()->ordered()->first();
        });
    }

    public function find(string $code): ?Language
    {
        return $this->all()->firstWhere('code', $code);
    }

    public function exists(string $code): bool
    {
        return $this->find($code) !== null;
    }

    /** Supported locale codes (cheap array). */
    public function codes(): array
    {
        return $this->all()->pluck('code')->all();
    }

    /**
     * Build the same URL but for a different locale.
     * Strips the current /{locale} prefix and prepends the target.
     */
    public function switchUrl(string $targetCode, ?string $currentUrl = null): string
    {
        $currentUrl = $currentUrl ?? request()->fullUrl();
        $parts = parse_url($currentUrl);
        $path  = $parts['path'] ?? '/';
        $query = isset($parts['query']) ? '?' . $parts['query'] : '';

        $codes = $this->codes();
        $segments = array_values(array_filter(explode('/', $path), fn ($s) => $s !== ''));

        if (!empty($segments) && in_array($segments[0], $codes, true)) {
            array_shift($segments);
        }

        $newPath = '/' . $targetCode . (empty($segments) ? '' : '/' . implode('/', $segments));
        $host = ($parts['scheme'] ?? 'http') . '://' . ($parts['host'] ?? request()->getHost())
              . (isset($parts['port']) ? ':' . $parts['port'] : '');

        return $host . $newPath . $query;
    }
}
