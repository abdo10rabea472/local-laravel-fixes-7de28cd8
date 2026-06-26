<footer class="bg-slate-950 text-slate-400 pt-14 pb-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10">
        <div class="lg:col-span-3 space-y-4">
            @if(site_setting_url('site_logo'))
            <img src="{{ site_setting_url('site_logo') }}" alt="UNI-LAB MARKET" class="h-10 w-auto brightness-0 invert opacity-90 object-contain">
            @else
            <span class="text-xl font-black text-white">UNI-LAB MARKET</span>
            @endif
            <p class="text-sm leading-relaxed max-w-sm">Your one-stop shop for professional educational tools and equipment across all university colleges.</p>
        </div>

        <div class="lg:col-span-3">
            <h5 class="text-white font-bold text-sm mb-4">Colleges</h5>
            <ul class="space-y-2 text-sm">
                @foreach(($navCategories ?? collect()) as $college)
                <li>
                    <a href="{{ route('category.show', $college->slug) }}" class="hover:text-violet-400 transition-colors inline-flex items-center gap-2">
                        @if($college->icon_url)
                            <img src="{{ $college->icon_url }}" alt="" class="h-4 w-4 object-contain rounded">
                        @endif
                        {{ $college->name }}
                    </a>
                    @if($college->children->isNotEmpty())
                    <ul class="mr-4 mt-1 space-y-1">
                        @foreach($college->children->take(4) as $child)
                        <li>
                            <a href="{{ route('category.show', $child->slug) }}" class="text-xs text-slate-500 hover:text-violet-400 transition-colors">
                                {{ $child->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>

        <div class="lg:col-span-2">
            <h5 class="text-white font-bold text-sm mb-4">Quick Links</h5>
            <ul class="space-y-2 text-sm">
                @if(($navFooterMenu ?? collect())->isNotEmpty())
                    @foreach($navFooterMenu as $item)
                        @if($item->type === 'coupon')
                            <li><a href="#" onclick="openWelcomePopup('{{ $item->coupon_code }}', {{ $item->coupon_percent ?? 0 }}); return false;" class="hover:text-violet-400 transition-colors">{{ $item->title }}</a></li>
                        @else
                            <li><a href="{{ $item->url }}" target="{{ $item->target }}" class="hover:text-violet-400 transition-colors">{{ $item->title }}</a></li>
                        @endif
                    @endforeach
                @else
                    <li><a href="{{ route('products.index') }}" class="hover:text-violet-400 transition-colors">All Products</a></li>
                    <li><a href="{{ url('/') }}#featured" class="hover:text-violet-400 transition-colors">Featured</a></li>
                    <li><a href="{{ url('/') }}#colleges" class="hover:text-violet-400 transition-colors">Shop by College</a></li>
                @endif
            </ul>
        </div>

        <div class="lg:col-span-2">
            <h5 class="text-white font-bold text-sm mb-4">Services</h5>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('pages.faqs') }}" class="hover:text-violet-400 transition-colors">FAQs</a></li>
                <li><a href="{{ route('pages.privacy') }}" class="hover:text-violet-400 transition-colors">Privacy Policy</a></li>
                <li><a href="{{ route('pages.returns') }}" class="hover:text-violet-400 transition-colors">Returns & Refunds</a></li>
            </ul>
        </div>

        <div class="lg:col-span-2 space-y-4">
            <h5 class="text-white font-bold text-sm">Contact</h5>
            <div class="text-sm space-y-2">
                <p class="flex items-center gap-2"><i class="fa-solid fa-envelope text-violet-500 w-4"></i> {{ site_setting('contact_email', 'ahmedkhamis@gmail.com') }}</p>
                <p class="flex items-center gap-2"><i class="fa-solid fa-phone text-violet-500 w-4"></i> {{ site_setting('contact_phone', '01007970340') }}</p>
                <p class="flex items-center gap-2"><i class="fa-solid fa-location-dot text-violet-500 w-4"></i> {{ site_setting('contact_address', 'El Minya, Egypt') }}</p>
            </div>
            <div class="flex gap-2 pt-2">
                <a href="#" class="h-9 w-9 rounded-lg bg-slate-800 flex items-center justify-center hover:bg-violet-600 transition-colors"><i class="fab fa-facebook-f text-sm"></i></a>
                <a href="#" class="h-9 w-9 rounded-lg bg-slate-800 flex items-center justify-center hover:bg-violet-600 transition-colors"><i class="fab fa-instagram text-sm"></i></a>
                <a href="#" class="h-9 w-9 rounded-lg bg-slate-800 flex items-center justify-center hover:bg-violet-600 transition-colors"><i class="fab fa-linkedin-in text-sm"></i></a>
            </div>
        </div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 pt-6 border-t border-slate-800 text-center text-xs text-slate-500">
        © {{ date('Y') }} UNI-LAB MARKET. All rights reserved.
    </div>
</footer>
