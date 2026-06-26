## الهدف
أتمتة كاملة لتدفّق الشحن: حساب التكلفة فور اختيار العنوان/الشركة، إنشاء شحنة Aramex تلقائياً عند تأكيد الطلب، حفظ كل بيانات الشحنة في الطلب، وعرض/طباعة البوليصة وإعادة المزامنة من الإدارة — مع تسجيل أي فشل وإمكانية إعادة المحاولة.

---

## 1) صفحة الـ Checkout (واجهة العميل)
- إزالة زر **"احسب تكلفة الشحن"** نهائياً من `resources/views/checkout/index.blade.php`.
- إضافة سكربت يستمع لتغيّر:
  - `country_id` / `region_id` / `city` / `postal_code` / `address_line1`
  - `shipping_carrier_id`
- يطلق **AJAX** إلى `POST /checkout/calculate-shipping` (موجود مسبقاً) مع debounce 400ms.
- تحديث **تلقائي** لقيم: تكلفة الشحن، الضريبة، الإجمالي — بدون reload.
- مؤشّر تحميل صغير بجوار قيمة الشحن أثناء الحساب.

## 2) إنشاء الطلب + إنشاء الشحنة تلقائياً
عند `CheckoutController@place`:
1. إنشاء الطلب داخل `DB::transaction`.
2. بعد commit، **dispatch** Job: `App\Jobs\CreateCarrierShipment($order)` على queue `shipping`.
3. الـ Job ينادي `ShippingDispatchService::createForOrder($order)`:
   - يحدّد الـ driver من `shipping_carrier.code` (`aramex` → `AramexService::createShipment`).
   - يبني الـ payload من بيانات الطلب + عنوان الشحن + إعدادات Shipper من `config/aramex.php`.
   - عند **النجاح**: يحفظ في الطلب الحقول الجديدة (انظر قسم Migration) ويغيّر `status = processing` و `shipping_status = shipment_created`.
   - عند **الفشل**: يحفظ `shipping_error`, `shipping_attempts++`, ويُبقي الطلب بحالة `pending_shipment`.
   - يسجّل في `Log::channel('shipping')` ويضيف صفّاً في `order_status_history`.

## 3) Migration جديدة — `add_carrier_shipment_fields_to_orders`
أعمدة جديدة على `orders`:
```text
shipment_number       string  nullable  index
tracking_number       string  nullable  index
shipping_status       string  nullable  default 'pending'
label_url             string  nullable
barcode               string  nullable
tracking_url          string  nullable
pickup_address        json    nullable
pickup_datetime       datetime nullable
carrier_response      json    nullable   -- raw response للمرجع
shipping_error        text    nullable
shipping_attempts     unsignedTinyInteger default 0
shipment_created_at   datetime nullable
```
+ indexes على `shipping_status`, `tracking_number`.

## 4) صفحة تفاصيل الطلب في الإدارة
في `resources/views/admin/orders/show.blade.php`:
- بطاقة **معلومات الشحنة** تعرض: شركة الشحن، رقم الشحنة، رقم التتبع، الحالة، الباركود (صورة)، عنوان وميعاد الاستلام، رابط التتبع (يفتح في تبويب جديد).
- أزرار:
  - **طباعة البوليصة** → يفتح `label_url` في نافذة جديدة جاهز للطباعة.
  - **تحميل البوليصة** → نفس الرابط بصفة `download`.
  - **إعادة محاولة إنشاء الشحنة** (يظهر فقط لو `shipping_error` موجود) → POST إلى `admin.orders.shipment.retry`.
  - **إعادة مزامنة الحالة** → POST إلى `admin.orders.shipment.sync` يستدعي `AramexService::trackShipments`.
- إذا `shipping_error` → بانر أحمر يعرض الرسالة + زر إعادة المحاولة.

## 5) Routes جديدة في `routes/web.php` (مجموعة admin)
```text
POST  /admin/orders/{order}/shipment/retry  → OrderController@retryShipment
POST  /admin/orders/{order}/shipment/sync   → OrderController@syncShipment
GET   /admin/orders/{order}/shipment/label  → OrderController@printLabel (proxy للطباعة)
```

## 6) ملفات/Classes جديدة
- `app/Jobs/CreateCarrierShipment.php` — Queueable, `tries=3`, backoff تصاعدي.
- `app/Services/ShippingDispatchService.php` — منسّق بين الـ Order وملقّمات الشحن (Strategy Pattern؛ يسهل إضافة شركات لاحقاً).
- `config/logging.php` — قناة `shipping` تكتب في `storage/logs/shipping.log`.

## 7) معالجة الأخطاء والـ Logs
- كل استدعاء SOAP محاط بـ try/catch.
- تسجيل: `order_id`, `carrier_code`, `payload`, `response/error` في القناة `shipping`.
- لو فشل الـ Job 3 مرات → يبقى الطلب `pending_shipment` ويُرسل إشعار للأدمن (بريد عبر `CustomerNotificationMail` بنسخة admin).

## 8) ما **لن** يتغيّر
- مخطط جدول `shipping_carriers`، صفحات Cart، نظام الكوبونات، RLS/Policies الموجودة.
- صفحة `checkout` الـ HTML يبقى تصميمها كما هو — فقط إزالة الزر + إضافة سكربت AJAX.

---

## تفاصيل تقنية مختصرة
- الـ Driver المُختار يُحدَّد من حقل `shipping_carriers.code` (`aramex`, `bosta`, …) عبر `match()`.
- بناء عنوان Aramex من `order.shipping_*` بالشكل: `line1`, `city`, `post_code`, `country_code` (الأخطاء السابقة التي ظهرت في tinker).
- استخدام `queue:work --queue=shipping,default` (تذكير في الـ README).
- لا حاجة لتغيير شيء في الـ Frontend خارج صفحة Checkout وصفحة Order في الإدارة.

هل أبدأ التنفيذ بهذا الشكل؟
