## خطة نظام العروض والخصومات الشامل

سأبني نظامًا متكاملًا للعروض والخصومات في لوحة التحكم مع التكامل الكامل مع الواجهة الأمامية وصفحة Checkout.

---

### 1) خصومات المنتجات (Product Discounts)

**قاعدة البيانات** — migration جديدة لجدول `product_discounts`:
- `id`, `product_id` (FK), `type` (percent|fixed), `value` (decimal)
- `starts_at`, `ends_at` (nullable)
- `is_active` (boolean), `timestamps`
- Index على `product_id` + `is_active`

**Model**: `ProductDiscount` بعلاقة `belongsTo(Product)`، و scope `active()` يفحص التواريخ.
**Product Model**: علاقة `hasOne(activeDiscount)` + accessors:
- `final_price` — السعر بعد الخصم
- `discount_percent` — النسبة المحسوبة للعرض في Badge
- `has_discount` — boolean

**لوحة التحكم** — `Admin/ProductDiscountController` + route group `/admin/discounts/products`:
- صفحة index بقائمة الخصومات + فلترة (نشط/منتهي)
- form إضافة/تعديل مع اختيار منتج (Select2 بحث)، نوع، قيمة، تواريخ، حالة
- إجراءات: تفعيل/تعطيل/حذف

**الواجهة الأمامية** — تحديث كل أماكن عرض المنتج:
- بطاقة المنتج (`product-card` component مشترك): شارة `-X%` + السعر القديم مشطوب + السعر الجديد
- صفحة المنتج (`product/show.blade.php`)
- نتائج البحث، صفحات الأقسام، المنتجات المشابهة
- قسم "العروض" في الصفحة الرئيسية: قائمة آخر المنتجات التي لديها `activeDiscount`، يتم تفعيله من إعدادات الصفحة الرئيسية

### 2) أكواد الخصم (Coupons)

**Migrations**:
- `coupons`: `id, code (unique), type (percent|fixed), value, starts_at, ends_at, min_order_total, max_discount_amount, usage_limit, used_count, scope (all|products|categories), is_active, timestamps`
- `coupon_products`: pivot
- `coupon_categories`: pivot
- `coupon_redemptions`: `id, coupon_id, user_id (nullable), email, phone, order_total, discount_amount, used_at` — لمنع التكرار

**Models**: `Coupon`, `CouponRedemption` مع علاقات + method `isValidFor($cart, $user, $email)` يعيد `[ok, message, discount]`.

**لوحة التحكم** — `Admin/CouponController` + `/admin/coupons`:
- index بقائمة + فلترة + بحث
- create/edit form كامل بكل الحقول + اختيار منتجات/أقسام عند `scope != all`
- تفعيل/تعطيل/حذف
- عرض عدد مرات الاستخدام `used_count / usage_limit`

### 3) تطوير نافذة الترحيب

في `welcome-popup.blade.php`:
- استبدال الكود الثابت بـ `Coupon::active()->inRandomOrder()->first()`
- عرض الكود + النسبة/القيمة + رسالة تشجيعية + زر نسخ
- إخفاء قسم الكود إذا لا يوجد كوبون فعّال

### 4) Checkout — حقل كود الخصم

**Route**: `POST /checkout/apply-coupon` (AJAX، يرجع JSON)
- التحقق: نشط، ضمن التواريخ، حد أدنى للطلب، scope منطبق على عناصر السلة، لم يستخدمه المستخدم/الإيميل/الهاتف من قبل، لم يتجاوز `usage_limit`
- يحسب الخصم (مع `max_discount_amount`)

**Frontend** في `checkout/index.blade.php`:
- input + زر "تطبيق"
- عند النجاح: عرض سطر "خصم الكوبون" في الملخص + تحديث الإجمالي مباشرة
- حفظ في localStorage حتى submit
- عند submit: إعادة التحقق server-side وإنشاء `coupon_redemption` + زيادة `used_count`

### 5) منع التكرار

داخل `Coupon::isValidFor()`:
- لو user مسجل → فحص `CouponRedemption::where(coupon_id, user_id)`
- لو زائر → فحص بالـ email أو phone من بيانات الطلب
- رسالة: "لقد استخدمت هذا الكود من قبل"

---

### التفاصيل التقنية

- جميع migrations تتبع Laravel 12 + indexes مناسبة
- Helper جديد: `product_final_price($product)`, `product_discount_badge($product)`
- Blade component مشترك `<x-product-card />` لتوحيد عرض الخصم في كل المواضع
- التحقق server-side دائمًا، الحساب client-side للعرض الفوري فقط
- ربط نافذة الترحيب بأكواد فعلية بدل القيم الثابتة في الإعدادات

### الملفات الرئيسية المضافة/المعدلة

**جديد**:
- 4 migrations (product_discounts, coupons, coupon_products/categories pivot, coupon_redemptions)
- Models: `ProductDiscount`, `Coupon`, `CouponRedemption`
- Controllers: `Admin/ProductDiscountController`, `Admin/CouponController`
- Views: `admin/discounts/products/{index,form}`, `admin/coupons/{index,form}`
- Component: `components/product-card.blade.php`

**معدّل**:
- `Product` model (علاقات + accessors)
- `routes/web.php` + `routes/admin` (إضافة المسارات)
- `welcome-popup.blade.php` (كود ديناميكي)
- `checkout/index.blade.php` + `CheckoutController` (تطبيق الكوبون + redemption عند submit)
- كل blade يعرض منتج: استخدام `<x-product-card>` أو إضافة منطق الخصم
- قائمة admin sidebar (روابط جديدة)

هل أبدأ التنفيذ؟
