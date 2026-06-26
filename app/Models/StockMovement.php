<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $guarded = [];

    protected $casts = [
        'quantity_change' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function typeLabel(): string
    {
        return [
            'manual' => 'تعديل يدوي',
            'order' => 'طلب جديد',
            'order_cancel' => 'إلغاء طلب',
            'return' => 'مرتجع',
            'adjustment' => 'تسوية مخزون',
            'bulk_update' => 'تحديث جماعي',
        ][$this->type] ?? $this->type;
    }

    public function typeColor(): string
    {
        return [
            'manual' => 'sky',
            'order' => 'rose',
            'order_cancel' => 'emerald',
            'return' => 'amber',
            'adjustment' => 'violet',
            'bulk_update' => 'indigo',
        ][$this->type] ?? 'slate';
    }
}
