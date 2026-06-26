<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRequestItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'restock' => 'boolean',
    ];

    public function returnRequest(): BelongsTo { return $this->belongsTo(ReturnRequest::class); }
    public function orderItem(): BelongsTo     { return $this->belongsTo(OrderItem::class); }
    public function product(): BelongsTo       { return $this->belongsTo(Product::class); }
}
