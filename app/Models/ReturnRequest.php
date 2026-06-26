<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnRequest extends Model
{
    public const STATUSES = ['pending','approved','rejected','received','refunded','cancelled'];

    protected $guarded = [];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function user(): BelongsTo  { return $this->belongsTo(User::class); }
    public function items(): HasMany   { return $this->hasMany(ReturnRequestItem::class); }

    public static function generateNumber(): string
    {
        return 'RMA-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    public function statusLabel(): string
    {
        return [
            'pending' => 'قيد المراجعة',
            'approved' => 'مقبول',
            'rejected' => 'مرفوض',
            'received' => 'تم الاستلام',
            'refunded' => 'تم رد المبلغ',
            'cancelled' => 'ملغي',
        ][$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return [
            'pending' => 'amber',
            'approved' => 'sky',
            'rejected' => 'rose',
            'received' => 'indigo',
            'refunded' => 'emerald',
            'cancelled' => 'slate',
        ][$this->status] ?? 'slate';
    }

    public function reasonLabel(): string
    {
        return [
            'defective' => 'منتج معيب',
            'wrong_item' => 'منتج خاطئ',
            'not_as_described' => 'مخالف للوصف',
            'damaged' => 'تالف عند الاستلام',
            'no_longer_wanted' => 'لم أعد أرغب به',
            'other' => 'أخرى',
        ][$this->reason] ?? $this->reason;
    }
}
