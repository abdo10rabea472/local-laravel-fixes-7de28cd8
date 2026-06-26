<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

/**
 * Centralised stock mutation API.
 * Every change to product.stock MUST go through here so we always have history.
 */
class StockService
{
    /**
     * Apply a delta and record a movement atomically.
     */
    public function apply(
        Product $product,
        int $delta,
        string $type,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $note = null,
        ?string $changedByType = null,
        ?int $changedById = null
    ): StockMovement {
        return DB::transaction(function () use (
            $product, $delta, $type, $referenceType, $referenceId, $note, $changedByType, $changedById
        ) {
            // Row-level lock to avoid race conditions
            $fresh = Product::whereKey($product->id)->lockForUpdate()->first(['id', 'stock']);
            $before = (int) $fresh->stock;
            $after = max(0, $before + $delta);

            Product::whereKey($product->id)->update(['stock' => $after]);

            return StockMovement::create([
                'product_id' => $product->id,
                'quantity_change' => $delta,
                'stock_before' => $before,
                'stock_after' => $after,
                'type' => $type,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'note' => $note,
                'changed_by_type' => $changedByType,
                'changed_by_id' => $changedById,
            ]);
        });
    }

    /**
     * Set absolute stock value (used for manual edits/adjustments).
     */
    public function setAbsolute(
        Product $product,
        int $newQty,
        string $type = 'adjustment',
        ?string $note = null,
        ?string $changedByType = 'admin',
        ?int $changedById = null
    ): StockMovement {
        $delta = $newQty - (int) $product->stock;
        return $this->apply($product, $delta, $type, null, null, $note, $changedByType, $changedById);
    }
}
