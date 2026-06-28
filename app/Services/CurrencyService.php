<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CurrencyService
{
    protected ?Currency $current = null;

    /** @return Collection<int, Currency> */
    public function all(): Collection
    {
        return Cache::rememberForever('currencies:all', function () {
            return Currency::query()->active()->ordered()->get();
        });
    }

    public function default(): ?Currency
    {
        return Cache::rememberForever('currencies:default', function () {
            return Currency::query()->active()->where('is_default', true)->first()
                ?? Currency::query()->active()->ordered()->first();
        });
    }

    public function find(string $code): ?Currency
    {
        return $this->all()->firstWhere('code', strtoupper($code));
    }

    public function setCurrent(?Currency $currency): void
    {
        $this->current = $currency;
    }

    public function current(): ?Currency
    {
        return $this->current ?? $this->default();
    }

    /**
     * Convert an amount stored in the DEFAULT currency to the target currency.
     * All product prices are assumed to be in the default currency.
     */
    public function convert(float $amount, ?Currency $to = null, ?Currency $from = null): float
    {
        $from = $from ?? $this->default();
        $to   = $to   ?? $this->current();
        if (!$from || !$to || $from->id === $to->id) {
            return $amount;
        }
        // amount_in_default = amount / from_rate ; target = amount_in_default * to_rate
        $inBase = $amount / (float) $from->exchange_rate;
        return $inBase * (float) $to->exchange_rate;
    }

    public function format(float $amount, ?Currency $currency = null): string
    {
        $currency = $currency ?? $this->current();
        if (!$currency) {
            return number_format($amount, 2);
        }
        return $currency->format($this->convert($amount, $currency));
    }
}
