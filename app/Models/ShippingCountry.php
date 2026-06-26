<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingCountry extends Model
{
    protected $fillable = ['name', 'cost', 'position', 'status'];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'position' => 'integer',
            'status' => 'boolean',
        ];
    }

    public function regions(): HasMany
    {
        return $this->hasMany(ShippingRegion::class, 'country_id')->orderBy('position')->orderBy('name');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
