<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\ShippingDispatchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateCarrierShipment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(public int $orderId) {}

    public function handle(ShippingDispatchService $dispatcher): void
    {
        $order = Order::with('carrier', 'items')->find($this->orderId);
        if (!$order) return;

        // Already created — skip.
        if ($order->shipment_number) return;

        $dispatcher->createForOrder($order);
    }

    public function failed(Throwable $e): void
    {
        Log::channel('shipping')->error('CreateCarrierShipment job failed', [
            'order_id' => $this->orderId,
            'error'    => $e->getMessage(),
        ]);
    }
}
