<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Coordinates outgoing shipment creation between an Order and the carrier
 * provider implementations. Pure orchestration — no SOAP/HTTP here.
 */
class ShippingDispatchService
{
    public function __construct(private AramexService $aramex) {}

    /**
     * Create a shipment with the order's selected carrier.
     * Persists every relevant field on the order and logs the outcome.
     */
    public function createForOrder(Order $order): array
    {
        $order->loadMissing('carrier', 'items');

        // No carrier selected → nothing to do, but mark order as not needing a shipment.
        if (!$order->carrier) {
            $order->forceFill(['shipping_status' => 'no_carrier'])->save();
            return ['ok' => true, 'message' => 'لا توجد شركة شحن مرتبطة بهذا الطلب.'];
        }

        $code = strtolower((string) $order->carrier->code);
        $result = match ($code) {
            'aramex' => $this->createAramexShipment($order),
            default  => ['ok' => false, 'message' => "لا يوجد تكامل API مع شركة الشحن: {$code}"],
        };

        $order->increment('shipping_attempts');

        if ($result['ok']) {
            $data = $result['data'] ?? [];
            $trackingNumber = $data['shipment_id'] ?? null;

            $order->forceFill([
                'shipment_number'     => $trackingNumber,
                'tracking_number'     => $trackingNumber,
                'label_url'           => $data['label_url'] ?? null,
                'barcode'             => $data['barcode'] ?? $trackingNumber,
                'tracking_url'        => $order->carrier->buildTrackingUrl($trackingNumber),
                'pickup_address'      => $data['pickup_address'] ?? null,
                'pickup_datetime'     => $data['pickup_datetime'] ?? null,
                'carrier_response'    => $this->safeRaw($data['raw'] ?? null),
                'shipping_status'     => 'shipment_created',
                'shipping_error'      => null,
                'shipment_created_at' => now(),
            ])->save();

            OrderStatusHistory::create([
                'order_id'        => $order->id,
                'from_status'     => $order->status,
                'to_status'       => $order->status,
                'note'            => "تم إنشاء شحنة لدى {$order->carrier->name} — رقم: {$trackingNumber}",
                'changed_by_type' => 'system',
            ]);

            Log::channel('shipping')->info('Shipment created', [
                'order_id'  => $order->id,
                'carrier'   => $code,
                'shipment'  => $trackingNumber,
            ]);
        } else {
            $order->forceFill([
                'shipping_status' => 'failed',
                'shipping_error'  => $result['message'] ?? 'Unknown error',
            ])->save();

            OrderStatusHistory::create([
                'order_id'        => $order->id,
                'from_status'     => $order->status,
                'to_status'       => $order->status,
                'note'            => 'فشل إنشاء الشحنة: ' . ($result['message'] ?? 'خطأ غير معروف'),
                'changed_by_type' => 'system',
            ]);

            Log::channel('shipping')->error('Shipment creation failed', [
                'order_id' => $order->id,
                'carrier'  => $code,
                'error'    => $result['message'] ?? null,
            ]);
        }

        return $result;
    }

    /** Resync live status of an existing shipment. */
    public function syncStatus(Order $order): array
    {
        $order->loadMissing('carrier');
        if (!$order->carrier || !$order->shipment_number) {
            return ['ok' => false, 'message' => 'لا توجد شحنة مسجّلة لإعادة المزامنة.'];
        }

        $code = strtolower((string) $order->carrier->code);
        if ($code !== 'aramex') {
            return ['ok' => false, 'message' => 'إعادة المزامنة متاحة حالياً مع Aramex فقط.'];
        }

        $res = $this->aramex->trackShipments([$order->shipment_number]);
        if (!$res['ok']) {
            Log::channel('shipping')->warning('Tracking sync failed', [
                'order_id' => $order->id,
                'error'    => $res['message'] ?? null,
            ]);
            return $res;
        }

        $order->forceFill([
            'tracking_history'      => $this->extractEvents($res['data'] ?? null),
            'tracking_last_sync_at' => now(),
        ])->save();

        return ['ok' => true, 'message' => 'تم تحديث حالة الشحنة.'];
    }

    private function createAramexShipment(Order $order): array
    {
        $shipperCfg = config('aramex.Shipper');

        $shipper = [
            'Reference1'   => $shipperCfg['Reference1'] ?? 'STORE',
            'AccountNumber'=> $shipperCfg['AccountNumber'] ?? config('aramex.ClientInfo.AccountNumber'),
            'PartyAddress' => $shipperCfg['PartyAddress'],
            'Contact'      => $shipperCfg['Contact'],
        ];

        $consignee = [
            'Reference1'   => 'ORD-' . $order->id,
            'AccountNumber'=> '',
            'PartyAddress' => [
                'Line1'               => $order->shipping_address ?: 'N/A',
                'Line2'               => '',
                'Line3'               => '',
                'City'                => $order->shipping_city ?: $order->shipping_region ?: 'N/A',
                'StateOrProvinceCode' => '',
                'PostCode'            => $order->shipping_postcode ?: '',
                'CountryCode'         => $this->guessCountryCode($order->shipping_country),
            ],
            'Contact' => [
                'PersonName'   => $order->customer_name ?: 'Customer',
                'CompanyName'  => $order->customer_name ?: 'Customer',
                'PhoneNumber1' => $order->phone ?: '+200000000',
                'CellPhone'    => $order->phone ?: '+200000000',
                'EmailAddress' => $order->email,
                'Type'         => '',
            ],
        ];

        $totalQty = (int) $order->items->sum('quantity');
        $weight   = max(0.5, $totalQty * 0.5);

        $shipment = [
            'Reference1'    => $order->order_number,
            'Shipper'       => $shipper,
            'Consignee'     => $consignee,
            'Details' => [
                'ActualWeight'      => ['Value' => $weight, 'Unit' => 'KG'],
                'NumberOfPieces'    => $totalQty ?: 1,
                'ProductGroup'      => 'DOM',
                'ProductType'       => 'OND',
                'PaymentType'       => 'P',
                'DescriptionOfGoods'=> 'Order ' . $order->order_number,
                'GoodsOriginCountry'=> $shipper['PartyAddress']['CountryCode'] ?? 'EG',
                'CashOnDeliveryAmount' => ['Value' => 0, 'CurrencyCode' => $order->currency ?: 'EGP'],
            ],
        ];

        return $this->aramex->createShipment($shipment);
    }

    private function guessCountryCode(?string $countryName): string
    {
        if (!$countryName) return 'EG';
        $map = [
            'Egypt' => 'EG', 'مصر' => 'EG',
            'Saudi Arabia' => 'SA', 'السعودية' => 'SA',
            'UAE' => 'AE', 'United Arab Emirates' => 'AE', 'الإمارات' => 'AE',
            'Kuwait' => 'KW', 'الكويت' => 'KW',
            'Qatar' => 'QA', 'قطر' => 'QA',
            'Jordan' => 'JO', 'الأردن' => 'JO',
            'Bahrain' => 'BH', 'البحرين' => 'BH',
            'Oman' => 'OM', 'عمان' => 'OM',
        ];
        return $map[trim($countryName)] ?? 'EG';
    }

    private function safeRaw($raw): ?array
    {
        if (!$raw) return null;
        try {
            return json_decode(json_encode($raw), true) ?: null;
        } catch (Throwable) {
            return null;
        }
    }

    private function extractEvents($raw): array
    {
        $events = [];
        try {
            $arr = json_decode(json_encode($raw), true);
            $results = $arr['TrackingResults']['KeyValueOfstringArrayOfTrackingResultmFAkxlpY'] ?? [];
            if (isset($results['Value'])) $results = [$results];
            foreach ($results as $bucket) {
                $list = $bucket['Value']['TrackingResult'] ?? [];
                if (isset($list['UpdateCode'])) $list = [$list];
                foreach ($list as $ev) {
                    $events[] = [
                        'at'          => $ev['UpdateDateTime'] ?? null,
                        'status'      => $ev['UpdateDescription'] ?? null,
                        'description' => $ev['Comments'] ?? null,
                        'location'    => $ev['UpdateLocation'] ?? null,
                    ];
                }
            }
        } catch (Throwable) {}
        return $events;
    }
}
