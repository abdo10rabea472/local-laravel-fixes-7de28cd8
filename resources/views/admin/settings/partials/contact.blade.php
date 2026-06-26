<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="space-y-2">
        <label class="text-xs font-bold text-slate-500">البريد الإلكتروني</label>
        <div class="relative">
            <i class="fa-solid fa-envelope absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="email" name="contact_email" value="{{ site_setting('contact_email') }}" class="w-full h-11 pr-10 pl-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
        </div>
    </div>
    <div class="space-y-2">
        <label class="text-xs font-bold text-slate-500">رقم الهاتف</label>
        <div class="relative">
            <i class="fa-solid fa-phone absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="contact_phone" value="{{ site_setting('contact_phone') }}" class="w-full h-11 pr-10 pl-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
        </div>
    </div>
    <div class="space-y-2 md:col-span-2">
        <label class="text-xs font-bold text-slate-500">العنوان</label>
        <div class="relative">
            <i class="fa-solid fa-location-dot absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="contact_address" value="{{ site_setting('contact_address') }}" class="w-full h-11 pr-10 pl-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
        </div>
    </div>
    <div class="space-y-2 md:col-span-2">
        <label class="text-xs font-bold text-slate-500">رقم الطلب (ID)</label>
        <div class="relative">
            <i class="fa-solid fa-hashtag absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="order_id_prefix" value="{{ site_setting('order_id_prefix', 'HZ-') }}" class="w-full h-11 pr-10 pl-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
        </div>
        <p class="text-xs text-slate-400 mt-1">يظهر كنص في الفاتورة إذا لم يتم رفع شعار.</p>
    </div>
</div>
