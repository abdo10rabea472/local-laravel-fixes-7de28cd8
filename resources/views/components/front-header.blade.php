{{-- Shopping Cart Sidebar --}}
<div class="cart">
  <div class="top_cart">
    <h2 class="text flex items-center gap-2">
      <i class="fa-solid fa-bag-shopping"></i>
      {{ __('app.nav_shopping_cart') }}
      <span class="cart-count">(0)</span>
    </h2>
    <span onclick="open_close_cart()" class="close_cart">
      <i class="fa-regular fa-circle-xmark"></i>
    </span>
  </div>
  <div class="items_in_cart"></div>
  <div class="bottom_cart">
    <div class="total">
      <p>{{ __('app.nav_cart_total') }}</p>
      <p class="price_cart_toral">{{ money(0) }}</p>
    </div>
    <div class="button_cart">
      <a href="{{ route('checkout') }}" class="btn_cart btn">{{ __("app.nav_checkout") }}</a>
      <span onclick="open_close_cart()" class="btn_cart trans_bg btn">{{ __("app.nav_shop_more") }}</span>
    </div>
  </div>
</div>

@php
  $currentCategorySlug = request()->routeIs('category.show') ? request()->route('slug') : null;
  $primaryColor = site_setting('primary_color', '#0b5cd6');
@endphp

<header class="sticky top-0 z-50 w-full">
  {{-- ═══ Announcement / utility bar ═══ --}}
  <section class="s_embed_code o_colored_level pb0 pt0 text-center" data-snippet="s_embed_code" data-name="Embed Code">
    <div class="eab-trust-bar">
      <div class="max-w-[1850px] mx-auto px-4 w-full flex items-center justify-between gap-3 h-full">

        {{-- الجزء الأيمن: شريط النصوص المتحرك (Ticker) --}}
        <div class="eab-ticker-wrapper">
          <div class="eab-ticker-track">
            @foreach([1, 2] as $i)
              {{-- 1. الشحن المجاني --}}
              @if(site_setting('free_shipping_enabled', '1') === '1' && site_setting('free_shipping_show_in_header', '1') === '1')
                <div class="eab-ticker-item eab-highlight">
                  <button type="button"
                    onclick="if (typeof openFreeShippingPopup === 'function') { openFreeShippingPopup(); }"
                    class="inline-flex items-center gap-1.5 font-bold text-amber-300 hover:text-amber-200 transition-colors bg-transparent border-0 p-0 cursor-pointer">
                    <i class="fa-solid fa-truck-fast text-[11px]"></i>
                    {{ __('app.nav_free_shipping_over', ['amount' => money(site_setting('free_shipping_threshold', 2000))]) }}
                  </button>
                </div>
              @endif

              {{-- 2. روابط القائمة العلوية المخصصة --}}
              @if(($navTopMenu ?? collect())->isNotEmpty())
                @foreach($navTopMenu as $item)
                  <div class="eab-ticker-item">
                    @if($item->type === 'coupon')
                      <a href="#"
                        onclick="openWelcomePopup('{{ $item->coupon_code }}', {{ $item->coupon_percent ?? 0 }}); return false;"
                        class="hover:text-amber-300 transition-colors">{{ $item->title }}</a>
                    @else
                      <a href="{{ $item->url }}" target="{{ $item->target }}"
                        class="hover:text-amber-300 transition-colors">{{ $item->title }}</a>
                    @endif
                  </div>
                @endforeach
              @endif
            @endforeach
          </div>
        </div>

        {{-- الجزء الأيسر: الأزرار التفاعلية الثابتة --}}
        <div class="flex items-center gap-3 shrink-0 z-50 eab-interactive-controls">
          {{-- مغير اللغة الثابت --}}
          @if(($availableLanguages ?? collect())->count() > 1)
            <div class="relative" x-data="{ open: false }">
              <button @click="open = !open" type="button"
                class="hover:text-amber-300 transition-colors inline-flex items-center gap-1.5 bg-transparent border-0 text-white font-semibold cursor-pointer text-xs">
                <i class="fa-solid fa-globe text-[11px]"></i>
                <span>{{ optional($currentLanguage ?? null)->native_name ?? strtoupper(app()->getLocale()) }}</span>
                <i class="fa-solid fa-chevron-down text-[8px]"></i>
              </button>
              <div x-show="open" x-cloak @click.outside="open=false"
                class="absolute left-0 mt-2 w-44 bg-white text-slate-700 rounded-xl shadow-xl border border-slate-100 py-1 z-[9999] text-right">
                @foreach($availableLanguages as $lang)
                  <a href="{{ route('locale.switch', $lang->code) }}"
                    class="flex items-center gap-2 px-3 py-2 text-xs hover:bg-blue-50 {{ app()->getLocale() === $lang->code ? 'font-bold text-blue-700' : '' }}">
                    @if($lang->flag)<img src="{{ asset('storage/' . $lang->flag) }}"
                    class="w-5 h-3.5 object-cover rounded">@endif
                    <span>{{ $lang->native_name }}</span>
                    <span class="mr-auto text-[10px] text-slate-400 uppercase">{{ $lang->code }}</span>
                  </a>
                @endforeach
              </div>
            </div>
            <span class="text-white/30 hidden sm:inline">|</span>
          @endif

          {{-- مغير العملة الثابت --}}
          @if(($availableCurrencies ?? collect())->count() > 1)
            @php $defaultCurrencyForRates = ($availableCurrencies ?? collect())->firstWhere('is_default', true) ?? current_currency(); @endphp
            <div class="relative" x-data="{ open: false }">
              <button @click="open = !open" type="button"
                class="hover:text-amber-300 transition-colors inline-flex items-center gap-1.5 bg-transparent border-0 text-white font-semibold cursor-pointer text-xs">
                <i class="fa-solid fa-coins text-[11px]"></i>
                <span>{{ optional($currentCurrency ?? null)->symbol }}
                  {{ optional($currentCurrency ?? null)->code ?? '' }}</span>
                <i class="fa-solid fa-chevron-down text-[8px]"></i>
              </button>
              <div x-show="open" x-cloak @click.outside="open=false"
                class="absolute left-0 mt-2 w-64 bg-white text-slate-700 rounded-xl shadow-xl border border-slate-100 py-1 z-[9999] text-right">
                @foreach($availableCurrencies as $cur)
                  <a href="{{ route('currency.switch', $cur->code) }}"
                    class="flex items-center justify-between gap-3 px-3 py-2 text-xs hover:bg-blue-50 {{ optional($currentCurrency ?? null)->code === $cur->code ? 'font-bold text-blue-700' : '' }}">
                    <span class="min-w-0 text-right">
                      <span class="block truncate">{{ $cur->name }}</span>
                      <span class="block text-[10px] text-slate-400 font-normal">
                        1 {{ $defaultCurrencyForRates?->code ?? $cur->code }} =
                        {{ rtrim(rtrim(number_format((float) $cur->exchange_rate, 8, '.', ''), '0'), '.') }}
                        {{ $cur->code }}
                      </span>
                    </span>
                    <span class="text-slate-400 shrink-0 text-left">{{ $cur->symbol }} {{ $cur->code }}</span>
                  </a>
                @endforeach
              </div>
            </div>
            <span class="text-white/30 hidden sm:inline">|</span>
          @endif

          {{-- أزرار الحساب / تسجيل الدخول الثابتة --}}
          @if(($navTopMenu ?? collect())->isEmpty())
            @guest('web')
              @if(!auth()->guard('admin')->check())
                <a href="{{ route('login') }}"
                  class="hover:text-amber-300 transition-colors text-xs">{{ __("app.shared_sign_in") }}</a>
                <span class="text-white/30 hidden sm:inline">|</span>
                <a href="{{ route('register') }}"
                  class="hover:text-amber-300 transition-colors text-xs hidden sm:inline">{{ __("app.nav_register") }}</a>
              @endif
            @endguest
            @auth('web')
              <a href="{{ route('account.dashboard') }}"
                class="hover:text-amber-300 transition-colors inline-flex items-center gap-1.5 text-xs">
                <i class="fa-solid fa-user-circle text-[11px]"></i> {{ __("app.nav_account") }}
              </a>
            @endauth
          @endif
        </div>

      </div>
    </div>
  </section>

  <style>
    .eab-trust-bar {
      position: relative;
      width: 100vw;
      left: 50%;
      right: 50%;
      margin-left: -50vw;
      margin-right: -50vw;
      /* التدرج الأزرق المتناسق مع كودك القديم */
      background: linear-gradient(90deg, #075985 0%, #2563eb 50%, #075985 100%);
      color: #f8fafc;
      font-family: 'Cairo', sans-serif;
      height: 42px;
      display: flex;
      align-items: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      direction: rtl;
      z-index: 999;
    }

    .eab-ticker-wrapper {
      flex: 1;
      overflow: hidden;
      display: flex;
      direction: ltr;
      mask-image: linear-gradient(to right, transparent, #000 20px, #000 95%, transparent);
      -webkit-mask-image: linear-gradient(to right, transparent, #000 20px, #000 95%, transparent);
    }

    .eab-ticker-track {
      display: flex;
      align-items: center;
      white-space: nowrap;
      animation: tickerScroll 30s linear infinite;
    }

    .eab-trust-bar:hover .eab-ticker-track {
      animation-play-state: paused;
    }

    .eab-ticker-item {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 13px;
      font-weight: 600;
      padding: 0 40px;
      letter-spacing: 0.3px;
      direction: rtl;
    }

    .eab-ticker-item a {
      color: inherit;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    /* تغيير لون الأيقونات العادية للأزرق السماوي الفاتح الجذاب والمناسب للخلفية الزرقاء */
    .eab-ticker-item i,
    .eab-interactive-controls i {
      color: #38bdf8;
    }

    /* تمييز إعلانات الشحن المجاني والعروض باللون الأصفر الذهبي الفاقع والواضح جداً على الأزرق */
    .eab-highlight,
    .eab-highlight button {
      color: #fde047 !important;
    }

    .eab-highlight i {
      color: #fde047 !important;
    }

    .eab-interactive-controls {
      direction: rtl;
      position: relative;
    }

    .eab-interactive-controls .relative {
      position: relative;
    }

    @keyframes tickerScroll {
      0% {
        transform: translateX(0);
      }

      100% {
        transform: translateX(-50%);
      }
    }

    @media (max-width: 768px) {
      .eab-trust-bar {
        height: 38px;
      }

      .eab-ticker-item {
        font-size: 12px;
        padding: 0 20px;
      }

      .eab-ticker-track {
        animation: tickerScroll 20s linear infinite;
      }
    }
  </style>

  {{-- ═══ Main header ═══ --}}
  <div class="bg-white/95 backdrop-blur-md border-b border-slate-200/70 shadow-sm">
    <div class="max-w-[1850px] mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex h-20 items-center justify-between gap-4 lg:gap-8">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="shrink-0 flex items-center gap-2.5 group">
          <div class="logo-box w-70 h-10 flex items-center justify-center">
            @if(site_setting_url('site_logo'))
              <img src="{{ site_setting_url('site_logo') }}" alt="UNI-LAB MARKET" class="h-10 w-auto drop-shadow-sm">
            @else
              <span
                class="w-11 h-11 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white grid place-items-center text-lg font-black shadow-lg shadow-blue-500/30 group-hover:scale-105 transition">
                <i class="fa-solid fa-flask-vial"></i>
              </span>
              <span class="flex flex-col leading-none">
                <span class="text-lg font-black text-slate-900 tracking-tight">UNI-LAB</span>
                <span class="text-[9px] font-bold text-blue-600 tracking-[0.2em] uppercase">Market</span>
              </span>
            @endif
          </div>
        </a>

        {{-- Search --}}
        <div class="hidden md:flex flex-1 max-w-2xl">
          <form action="{{ route('products.index') }}" method="get" class="relative w-full group">
            <input type="search" name="search" value="{{ request('search') }}"
              class="w-full h-12 pl-12 pr-4 bg-slate-100 border border-transparent focus:border-blue-400 focus:bg-white rounded-full text-sm outline-none transition-all focus:ring-3 focus:ring-blue-100"
              placeholder="{{ __('app.nav_search_long_placeholder') }}">
            <button type="submit"
              class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors">
              <i class="fa-solid fa-magnifying-glass"></i>
            </button>
          </form>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
          @if(auth()->guard('admin')->check())
            <a href="{{ route('admin.dashboard') }}"
              class="hidden lg:inline-flex items-center gap-2 h-11 px-4 rounded-full text-xs font-bold text-white bg-gradient-to-r from-sky-800 to-blue-600 hover:opacity-90 transition shadow-md shadow-blue-500/30">
              <i class="fa-solid fa-user-shield"></i> {{ __("app.nav_admin_panel") }}
            </a>
          @endif

          @guest('web')
            @if(!auth()->guard('admin')->check())
              <a href="{{ route('login') }}"
                class="hidden sm:inline-flex items-center gap-2 h-11 px-4 rounded-full text-sm font-bold text-slate-700 border border-slate-200 hover:border-blue-400 hover:text-blue-700 hover:bg-blue-50/50 transition">
                <i class="fa-solid fa-right-to-bracket text-xs"></i>
                <span>{{ __("app.shared_sign_in") }}</span>
              </a>
              <a href="{{ route('register') }}"
                class="hidden md:inline-flex items-center gap-2 h-11 px-4 rounded-full text-sm font-bold text-white bg-gradient-to-r from-sky-800 to-blue-600 hover:opacity-90 transition shadow-md shadow-blue-500/30">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>{{ __("app.nav_register") }}</span>
              </a>
            @endif
          @endguest

          @auth('web')
            <div class="relative group hidden sm:block">
              <button type="button"
                class="flex items-center gap-2 h-11 pl-1.5 pr-3 rounded-full bg-slate-50 hover:bg-blue-50 text-slate-700 transition-colors border border-slate-200">
                <span
                  class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex items-center justify-center text-xs font-black">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                <span class="text-sm font-bold hidden lg:inline max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
              </button>
              <div class="absolute right-0 top-full pt-2 z-50 hidden group-hover:block min-w-[240px]">
                <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden p-2">
                  <div class="px-3 py-2 mb-1 border-b border-slate-100">
                    <div class="text-sm font-black text-slate-900 truncate">{{ auth()->user()->name }}</div>
                    <div class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email }}</div>
                  </div>
                  <a href="{{ route('account.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:bg-blue-50 hover:text-blue-700"><i
                      class="fa-solid fa-gauge-high w-5 text-blue-500"></i> {{ __('app.nav_dashboard') }}</a>
                  <a href="{{ route('account.orders') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:bg-blue-50 hover:text-blue-700"><i
                      class="fa-solid fa-receipt w-5 text-blue-500"></i> {{ __('app.nav_my_orders') }}</a>
                  <a href="{{ route('account.returns.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:bg-blue-50 hover:text-blue-700"><i
                      class="fa-solid fa-rotate-left w-5 text-blue-500"></i> {{ __('app.nav_returns') }}</a>
                  <a href="{{ route('account.reviews') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:bg-blue-50 hover:text-blue-700"><i
                      class="fa-solid fa-star w-5 text-blue-500"></i> {{ __('app.shared_reviews') }}</a>
                  <a href="{{ route('wishlist.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:bg-blue-50 hover:text-blue-700"><i
                      class="fa-solid fa-heart w-5 text-rose-500"></i> {{ __('app.nav_wishlist') }}</a>
                  <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:bg-blue-50 hover:text-blue-700"><i
                      class="fa-solid fa-user-pen w-5 text-blue-500"></i> {{ __('app.nav_profile') }}</a>
                  <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 mt-1 pt-1">@csrf
                    <button
                      class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold text-rose-600 hover:bg-rose-50 text-right"><i
                        class="fa-solid fa-arrow-right-from-bracket w-5"></i> {{ __('app.nav_logout') }}</button>
                  </form>
                </div>
              </div>
            </div>
          @endauth

          {{-- Cart --}}
          <button type="button" onclick="open_close_cart()"
            class="relative flex items-center gap-2 h-11 px-3 sm:px-4 rounded-full bg-gradient-to-r from-sky-800 to-blue-600 hover:opacity-90 transition shadow-md shadow-blue-500/30 text-white">
            <i class="fa-solid fa-cart-shopping text-base"></i>
            <span class="hidden sm:inline text-sm font-bold">{{ __('app.shared_cart') }}</span>
            <span id="cart-count"
              class="absolute -top-1 -right-1 h-5 min-w-[20px] px-1 flex items-center justify-center rounded-full text-[10px] font-bold text-blue-700 bg-amber-300 ring-2 ring-white">0</span>
          </button>

          <button type="button" data-mobile-menu-toggle aria-controls="site-mobile-menu" aria-expanded="false"
            aria-label="Open menu"
            class="lg:hidden h-11 w-11 flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
            <i data-mobile-menu-icon class="fa-solid fa-bars text-lg"></i>
          </button>
        </div>
      </div>
    </div>

    {{-- ═══ Navigation bar ═══ --}}
    <div class="hidden lg:block border-t border-slate-100 bg-white">
      <div class="max-w-[1850px] mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex flex-nowrap items-center gap-1 h-12 whitespace-nowrap">
          {{-- Categories mega menu --}}
          @if(($navCategories ?? collect())->isNotEmpty())
            <div class="relative group" id="colleges-dropdown">
              <button type="button" id="colleges-dropdown-btn" aria-expanded="false" aria-haspopup="true"
                class="shrink-0 flex items-center gap-2 pl-3 pr-4 h-9 my-1.5 rounded-2xl text-sm font-bold text-white bg-gradient-to-r from-sky-800 to-blue-600 hover:opacity-90 transition shadow-md shadow-blue-500/30 text-white transition shadow-sm">
                <i class="fa-solid fa-bars-staggered text-xs"></i>
                <span>{{ __('app.shared_all_colleges') }}</span>
                <i id="colleges-dropdown-chevron"
                  class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200"></i>
              </button>

              <div id="colleges-dropdown-panel"
                class="absolute start-0 top-full pt-2 z-50 hidden opacity-0 translate-y-1 pointer-events-none transition-all duration-200 lg:group-hover:block lg:group-hover:opacity-100 lg:group-hover:translate-y-0 lg:group-hover:pointer-events-auto"
                style="width: min(760px, calc(100vw - 2rem)); max-width: calc(100vw - 2rem);">
                <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden">
                  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-5 py-3 border-b border-slate-100">
                    <div class="text-xs font-black uppercase tracking-wider text-blue-700">
                      {{ __('app.nav_browse_by_college') }}</div>
                  </div>
                  <div class="max-h-[min(70vh,520px)] overflow-y-auto overscroll-contain p-4">
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-3">
                      @foreach($navCategories ?? [] as $college)
                        <div
                          class="rounded-xl border border-slate-100 p-3 hover:border-blue-300 hover:shadow-md transition {{ $currentCategorySlug === $college->slug ? 'border-blue-300 bg-blue-50/40' : '' }}">
                          <a href="{{ route('category.show', $college->slug) }}"
                            class="flex items-center gap-3 group/college">
                            <div class="h-11 w-11 rounded-xl flex items-center justify-center shrink-0 shadow-sm text-white"
                              style="background: linear-gradient(135deg, {{ $college->primary_color ?? '#3a79ed' }}, {{ $college->secondary_color ?? '#6366f1' }});">
                              @if($college->icon_url)
                                <img src="{{ $college->icon_url }}" alt=""
                                  class="h-7 w-7 object-contain bg-white/90 rounded-lg p-0.5">
                              @else
                                <i class="fa-solid fa-graduation-cap"></i>
                              @endif
                            </div>
                            <div class="flex-1 min-w-0">
                              <div class="text-sm font-black text-slate-800 group-hover/college:text-blue-700 truncate">
                                {{ $college->name }}</div>
                              <div class="text-[10px] text-slate-400 font-semibold">{{ $college->children_count }}
                                {{ __('app.shared_departments') }}</div>
                            </div>
                            <i
                              class="fa-solid fa-arrow-right text-[10px] text-slate-300 group-hover/college:text-blue-600 group-hover/college:translate-x-0.5 transition shrink-0"></i>
                          </a>

                          @if($college->children->isNotEmpty())
                            <div class="flex flex-wrap gap-1 mt-2.5 pl-14">
                              @foreach($college->children->take(5) as $child)
                                <a href="{{ route('category.show', $child->slug) }}"
                                  class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-md border transition max-w-full truncate
                                                                  {{ $currentCategorySlug === $child->slug ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-400 hover:text-blue-700' }}">
                                  {{ $child->name }}
                                </a>
                              @endforeach
                            </div>
                          @endif
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endif

          <a href="{{ route('home') }}"
            class="shrink-0 flex items-center gap-1.5 px-4 h-12 text-sm font-bold {{ request()->routeIs('home') ? 'text-blue-700 border-b-2 border-blue-600' : 'text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300' }} transition">
            <i class="fa-solid fa-house text-xs"></i> {{ __('app.shared_home') }}
          </a>
          <a href="{{ route('products.index') }}"
            class="shrink-0 flex items-center gap-1.5 px-4 h-12 text-sm font-bold {{ request()->routeIs('products.*') ? 'text-blue-700 border-b-2 border-blue-600' : 'text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300' }} transition">
            <i class="fa-solid fa-box-open text-xs"></i> {{ __('app.shared_all_products') }}
          </a>
          <a href="{{ route('offers') }}"
            class="shrink-0 flex items-center gap-1.5 px-4 h-12 text-sm font-bold {{ request()->routeIs('offers') ? 'text-rose-600 border-b-2 border-rose-500' : 'text-rose-600 hover:text-rose-700 border-b-2 border-transparent hover:border-rose-300' }} transition">
            <i class="fa-solid fa-fire text-xs"></i> {{ __('app.shared_offers') }}
          </a>
          <a href="{{ route('blog.index') }}"
            class="shrink-0 flex items-center gap-1.5 px-4 h-12 text-sm font-bold {{ request()->routeIs('blog.*') ? 'text-blue-700 border-b-2 border-blue-600' : 'text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300' }} transition">
            <i class="fa-solid fa-newspaper text-xs"></i> {{ __('app.shared_blog') }}
          </a>
          <a href="{{ route('track-order') }}"
            class="shrink-0 flex items-center gap-1.5 px-4 h-12 text-sm font-bold {{ request()->routeIs('track-order') ? 'text-blue-700 border-b-2 border-blue-600' : 'text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300' }} transition">
            <i class="fa-solid fa-truck text-xs"></i> {{ __('app.shared_track_order') }}
          </a>
          <a href="{{ route('contact') }}"
            class="shrink-0 flex items-center gap-1.5 px-4 h-12 text-sm font-bold {{ request()->routeIs('contact') ? 'text-blue-700 border-b-2 border-blue-600' : 'text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300' }} transition">
            <i class="fa-solid fa-envelope text-xs"></i> {{ __('app.nav_contact') }}
          </a>
          <a href="{{ route('about') }}"
            class="hidden xl:flex items-center gap-1.5 px-4 h-12 text-sm font-bold {{ request()->routeIs('about') ? 'text-blue-700 border-b-2 border-blue-600' : 'text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300' }} transition">
            <i class="fa-solid fa-circle-info text-xs"></i> {{ __('app.nav_about') }}
          </a>

          {{-- Dynamic menu items --}}
          @foreach($navHeaderMenu ?? collect() as $item)
            @if($item->children->isNotEmpty())
              <div class="relative group shrink-0">
                <button type="button"
                  class="shrink-0 flex items-center gap-1.5 px-4 h-12 text-sm font-bold text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300 transition">
                  @if($item->icon)<i class="fa-solid {{ $item->icon }} text-xs"></i>@endif
                  <span>{{ $item->title }}</span>
                  <i
                    class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200 group-hover:rotate-180"></i>
                </button>
                <div
                  class="absolute left-0 top-full pt-2 z-50 hidden opacity-0 translate-y-1 pointer-events-none transition-all duration-200 lg:group-hover:block lg:group-hover:opacity-100 lg:group-hover:translate-y-0 lg:group-hover:pointer-events-auto min-w-[220px]">
                  <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden p-2">
                    @foreach($item->children as $child)
                      @if($child->type === 'coupon')
                        <a href="#"
                          onclick="openWelcomePopup('{{ $child->coupon_code }}', {{ $child->coupon_percent ?? 0 }}); return false;"
                          class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:text-blue-700 hover:bg-blue-50 transition">
                          <i class="fa-solid fa-gift ml-2 text-rose-400"></i>
                          {{ $child->title }}
                        </a>
                      @else
                        <a href="{{ $child->url }}" target="{{ $child->target }}"
                          class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:text-blue-700 hover:bg-blue-50 transition">
                          @if($child->icon)<i class="fa-solid {{ $child->icon }} ml-2 text-slate-400"></i>@endif
                          {{ $child->title }}
                        </a>
                      @endif
                    @endforeach
                  </div>
                </div>
              </div>
            @else
              @if($item->type === 'coupon')
                <a href="#"
                  onclick="openWelcomePopup('{{ $item->coupon_code }}', {{ $item->coupon_percent ?? 0 }}); return false;"
                  class="shrink-0 flex items-center gap-1.5 px-4 h-12 text-sm font-bold text-rose-600 hover:text-rose-700 border-b-2 border-transparent hover:border-rose-400 transition">
                  <i class="fa-solid fa-gift text-xs"></i>
                  <span>{{ $item->title }}</span>
                </a>
              @else
                <a href="{{ $item->url }}" target="{{ $item->target }}"
                  class="shrink-0 flex items-center gap-1.5 px-4 h-12 text-sm font-bold text-slate-600 hover:text-blue-700 border-b-2 border-transparent hover:border-blue-300 transition">
                  @if($item->icon)<i class="fa-solid {{ $item->icon }} text-xs"></i>@endif
                  <span>{{ $item->title }}</span>
                </a>
              @endif
            @endif
          @endforeach

        </nav>
      </div>
    </div>
  </div>

  {{-- ═══ Mobile Menu ═══ --}}
  <div id="site-mobile-menu" data-mobile-menu-panel
    class="lg:hidden hidden bg-white border-b border-slate-200 max-h-[85vh] overflow-y-auto shadow-lg">
    <div class="px-4 py-4 space-y-4">
      <form action="{{ route('products.index') }}" method="get" class="relative">
        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
        <input type="search" name="search" value="{{ request('search') }}"
          placeholder="{{ __('app.shared_search_placeholder') }}"
          class="w-full h-12 pl-12 pr-4 bg-slate-100 border border-transparent focus:border-blue-400 focus:bg-white rounded-full text-sm outline-none transition-all focus:ring-3 focus:ring-blue-100">
      </form>

      @if(auth()->guard('admin')->check())
        <a href="{{ route('admin.dashboard') }}"
          class="flex items-center justify-center gap-2 h-11 px-4 rounded-full text-xs font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:opacity-90 transition shadow-md shadow-blue-500/30">
          <i class="fa-solid fa-user-shield"></i> {{ __("app.nav_admin_panel") }}
        </a>
      @endif

      @guest('web')
        @if(!auth()->guard('admin')->check())
          <div class="grid grid-cols-2 gap-2">
            <a href="{{ route('login') }}"
              class="h-11 flex items-center justify-center gap-2 rounded-xl border border-slate-200 text-sm font-bold text-slate-700">
              <i class="fa-solid fa-right-to-bracket text-xs"></i> {{ __("app.shared_sign_in") }}
            </a>
            <a href="{{ route('register') }}"
              class="h-11 flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-bold shadow-md">
              <i class="fa-solid fa-user-plus text-xs"></i> {{ __("app.nav_register") }}
            </a>
          </div>
        @endif
      @endguest

      @auth('web')
        <details class="group rounded-2xl border border-slate-100 bg-white overflow-hidden">
          <summary class="flex items-center gap-3 px-3 py-3 cursor-pointer list-none hover:bg-slate-50">
            <span
              class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex items-center justify-center text-sm font-black shrink-0">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
            <div class="min-w-0 flex-1">
              <div class="text-sm font-black text-slate-900 truncate">{{ auth()->user()->name }}</div>
              <div class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email }}</div>
            </div>
            <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform group-open:rotate-180"></i>
          </summary>
          <div class="p-2 space-y-1 border-t border-slate-100 bg-slate-50/50">
            <a href="{{ route('account.dashboard') }}"
              class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:bg-white hover:text-blue-700"><i
                class="fa-solid fa-gauge-high w-5 text-blue-500"></i> {{ __('app.nav_dashboard') }}</a>
            <a href="{{ route('account.orders') }}"
              class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:bg-white hover:text-blue-700"><i
                class="fa-solid fa-receipt w-5 text-blue-500"></i> {{ __('app.nav_my_orders') }}</a>
            <a href="{{ route('account.returns.index') }}"
              class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:bg-white hover:text-blue-700"><i
                class="fa-solid fa-rotate-left w-5 text-blue-500"></i> {{ __('app.nav_returns') }}</a>
            <a href="{{ route('account.reviews') }}"
              class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:bg-white hover:text-blue-700"><i
                class="fa-solid fa-star w-5 text-blue-500"></i> {{ __('app.shared_reviews') }}</a>
            <a href="{{ route('wishlist.index') }}"
              class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:bg-white hover:text-blue-700"><i
                class="fa-solid fa-heart w-5 text-rose-500"></i> {{ __('app.nav_wishlist') }}</a>
            <a href="{{ route('profile.edit') }}"
              class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:bg-white hover:text-blue-700"><i
                class="fa-solid fa-user-pen w-5 text-blue-500"></i> {{ __('app.nav_profile') }}</a>
            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 mt-1 pt-1">@csrf
              <button
                class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold text-rose-600 hover:bg-rose-50 text-right"><i
                  class="fa-solid fa-arrow-right-from-bracket w-5"></i> {{ __('app.nav_logout') }}</button>
            </form>
          </div>
        </details>
      @endauth

      <nav class="space-y-1">
        <a href="{{ route('home') }}"
          class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 font-semibold text-slate-700">
          <i class="fa-solid fa-house w-5 text-blue-600"></i> {{ __('app.shared_home') }}
        </a>
        <a href="{{ route('products.index') }}"
          class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 font-semibold text-slate-700">
          <i class="fa-solid fa-box-open w-5 text-blue-600"></i> {{ __('app.shared_all_products') }}
        </a>
        <a href="{{ route('offers') }}"
          class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 font-semibold text-rose-600">
          <i class="fa-solid fa-fire w-5 text-rose-500"></i> {{ __('app.shared_offers') }}
        </a>
        <a href="{{ route('blog.index') }}"
          class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 font-semibold text-slate-700">
          <i class="fa-solid fa-newspaper w-5 text-blue-600"></i> {{ __('app.shared_blog') }}
        </a>
        <a href="{{ route('track-order') }}"
          class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 font-semibold text-slate-700">
          <i class="fa-solid fa-truck w-5 text-blue-600"></i> {{ __('app.shared_track_order') }}
        </a>
        <a href="{{ route('contact') }}"
          class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 font-semibold text-slate-700">
          <i class="fa-solid fa-envelope w-5 text-blue-600"></i> {{ __('app.nav_contact') }}
        </a>
        <a href="{{ route('about') }}"
          class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 font-semibold text-slate-700">
          <i class="fa-solid fa-circle-info w-5 text-blue-600"></i> {{ __('app.nav_about') }}
        </a>

        @foreach($navHeaderMenu ?? collect() as $item)
          @if($item->children->isNotEmpty())
            <details class="group rounded-xl border border-slate-100 overflow-hidden">
              <summary
                class="flex items-center gap-3 px-4 py-3 cursor-pointer list-none hover:bg-slate-50 font-semibold text-slate-700">
                @if($item->icon)<i class="fa-solid {{ $item->icon }} w-5 text-blue-600"></i>@endif
                <span class="flex-1">{{ $item->title }}</span>
                <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform group-open:rotate-180"></i>
              </summary>
              <div class="px-4 pb-3 space-y-1 border-t border-slate-100 bg-slate-50/50">
                @foreach($item->children as $child)
                  @if($child->type === 'coupon')
                    <a href="#"
                      onclick="openWelcomePopup('{{ $child->coupon_code }}', {{ $child->coupon_percent ?? 0 }}); return false;"
                      class="block px-3 py-2 text-sm rounded-lg hover:bg-white text-slate-600">
                      <i class="fa-solid fa-gift ml-1 text-rose-400"></i> {{ $child->title }}
                    </a>
                  @else
                    <a href="{{ $child->url }}" target="{{ $child->target }}"
                      class="block px-3 py-2 text-sm rounded-lg hover:bg-white text-slate-600">
                      {{ $child->title }}
                    </a>
                  @endif
                @endforeach
              </div>
            </details>
          @else
            @if($item->type === 'coupon')
              <a href="#"
                onclick="openWelcomePopup('{{ $item->coupon_code }}', {{ $item->coupon_percent ?? 0 }}); return false;"
                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 font-semibold text-slate-700">
                <i class="fa-solid fa-gift w-5 text-rose-500"></i>
                {{ $item->title }}
              </a>
            @else
              <a href="{{ $item->url }}" target="{{ $item->target }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 font-semibold text-slate-700">
                @if($item->icon)<i class="fa-solid {{ $item->icon }} w-5 text-blue-600"></i>@endif
                {{ $item->title }}
              </a>
            @endif
          @endif
        @endforeach

        @if(($navCategories ?? collect())->isNotEmpty())
          <div class="pt-2 border-t border-slate-100">
            <p class="text-xs font-black text-slate-400 uppercase tracking-wider px-4 mb-2">
              {{ __('app.shared_all_colleges') }}</p>
            @foreach($navCategories ?? [] as $college)
              <a href="{{ route('category.show', $college->slug) }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 font-semibold text-slate-700">
                <div class="h-9 w-9 rounded-xl flex items-center justify-center text-white text-[10px] font-black"
                  style="background: linear-gradient(135deg, {{ $college->primary_color ?? '#7c3aed' }}, {{ $college->secondary_color ?? '#6366f1' }});">
                  <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <span class="flex-1">{{ $college->name }}</span>
                <span class="text-[10px] text-slate-400">{{ $college->children_count }}</span>
              </a>
            @endforeach
          </div>
        @endif
      </nav>
    </div>
  </div>
</header>