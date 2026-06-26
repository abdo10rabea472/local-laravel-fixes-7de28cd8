<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingRegion extends Model
{
    protected $fillable = ['country_id', 'name', 'cost', 'position', 'status'];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'position' => 'integer',
            'status' => 'boolean',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(ShippingCountry::class, 'country_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
