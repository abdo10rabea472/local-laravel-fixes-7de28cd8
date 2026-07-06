@extends('layouts.front')

@section('content')

  {{-- ═══════════════ HERO ═══════════════ --}}
  <section
    class="relative overflow-hidden bg-slate-950 text-white min-h-[600px] flex items-center border-b border-white/5">
    <!-- خلفية متدرجة داكنة (Gradient Background) -->
    <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-blue-950 to-slate-950"></div>
    <!-- صورة خلفية مع تأثير Overlay (شفافية + مزج) -->
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=2071')] 
                              bg-cover bg-center opacity-20 mix-blend-overlay"></div>
    </div>

    <div
      class="relative z-10 max-w-[1850px] mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center w-full">
      <div>
        <span class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md border border-white/20 
                                              rounded-full px-6 py-2.5 text-sm text-white mb-8">
          <i class="fa-solid fa-flask-vial text-amber-300"></i>
          {{ __('app.home_hero_badge') }}
        </span>

        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.1] mb-6">
          <span class="block text-white mb-2">
            {{ __('app.home_hero_title') }}
          </span>

          <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-cyan-400">
            {{ __('app.home_hero') }}
          </span>
        </h1>

        <p class="text-slate-300 text-base sm:text-lg max-w-xl mb-8 leading-relaxed font-light">
          {{ __('app.home_hero_subtitle') }}
        </p>

        <div class="flex flex-wrap gap-4 mb-12">
          <a href="{{ route('products.index') }}" class="group inline-flex items-center justify-center gap-3 bg-amber-400 hover:bg-amber-500 
                                          text-slate-950 font-semibold text-lg px-10 py-4 rounded-2xl transition-all 
                                          duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-amber-500/30">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1v-5m10-10l2 2m-2-2v10a1 1 0 01-1 1v-5m-6 0a1 1 0 001-1v5" />
            </svg> {{ __('app.home_hero_shop_all') }}
          </a>
          <a href="#colleges" class="group inline-flex items-center justify-center gap-3 border-2 border-white/70 hover:border-white 
                                      text-white font-semibold text-lg px-10 py-4 rounded-2xl transition-all duration-300 
                                      hover:bg-white/10">
            <!-- أيقونة سهم -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform group-hover:translate-x-1"
              fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7-7 7" />
            </svg> {{ __('app.home_hero_browse_colleges') }}
          </a>
        </div>

        <div class="grid grid-cols-3 gap-4 max-w-md border-t border-white/10 pt-6">
          <div class="text-left">
            <div class="text-3xl lg:text-4xl font-bold text-white mb-1">{{ $hero['stat_products'] ?? '0' }}
            </div>
            <div class="text-slate-400 text-lg">{{ __('app.home_hero_stat_products') }}</div>
          </div>
          <div class="text-left border-x border-white/10 px-4">
            <div class="text-3xl lg:text-4xl font-bold text-white mb-1">{{ $hero['stat_colleges'] ?? '0' }}
            </div>
            <div class="text-slate-400 text-lg">{{ __('app.home_hero_stat_colleges') }}</div>
          </div>
          <div class="text-left px-4">
            <div class="text-3xl lg:text-4xl font-bold text-white mb-1">{{ $hero['stat_departments'] ?? '0' }}
            </div>
            <div class="text-slate-400 text-lg">{{ __('app.home_hero_stat_departments') }}</div>
          </div>
        </div>
      </div>

      <div class="relative hidden lg:flex items-center justify-center">
        <div class="relative grid grid-cols-2 gap-4 w-full max-w-lg">
          @php
            $heroIcons = [
              ['fa-microscope', 'from-blue-500/20 to-cyan-500/20 text-cyan-400', __('app.home_hero_card_microscopes')],
              ['fa-vial', 'from-purple-500/20 to-indigo-500/20 text-purple-400', __('app.home_hero_card_glassware')],
              ['fa-stethoscope', 'from-emerald-500/20 to-teal-500/20 text-emerald-400', __('app.home_hero_card_medical')],
              ['fa-screwdriver-wrench', 'from-amber-500/20 to-orange-500/20 text-amber-400', __('app.home_hero_card_engineering')],
            ];
          @endphp
          @foreach($heroIcons as [$ic, $grad, $lbl])
            <div
              class="group rounded-2xl bg-slate-900/40 backdrop-blur-md border border-white/10 p-6 transition-all duration-300 hover:border-white/20 hover:-translate-y-1">
              <div
                class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $grad }} flex items-center justify-center text-xl mb-4 border border-white/10 shadow-inner">
                <i class="fa-solid {{ $ic }}"></i>
              </div>
              <div class="font-bold text-slate-200 group-hover:text-white transition-colors">{{ $lbl }}</div>
              <div class="text-[11px] text-slate-400 mt-1">{{ __('app.home_hero_card_subtitle') }}</div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </section>
  {{-- ═══════════════ سلايدات عروض وخصومات ═══════════════ --}}
  @php
    // مصفوفة البيانات المخصصة لعروض وخصومات متجر أدوات الكليات العملية
    $slides = [
      [
        "id" => 1,
        "badge_icon" => "fa-fire",
        "badge_text" => "🔥 عرض حصري",
        "title_white" => "خصم فوري 10%",
        "title_gradient" => "على سلة مشترياتك",
        "desc" => "استمتع بخصم تلقائي 10% (بحد أقصى 50 جنيه) عند تسوقك منتجات بقيمة 2000 جنيه أو أكثر.",
        "link" => "/shop/offers",
        "btn_text" => "تسوق العروض الآن",
        "type" => "standard",
        "bg_gradient" => "from-neutral-950 via-stone-900 to-neutral-950",
        "image" => "https://m.media-amazon.com/images/I/71WXyKg87NL._AC_SX569_.jpg",
        "image_position" => "right",
        "active" => true
      ],
      [
        "id" => 2,
        "badge_icon" => "fa-gift",
        "badge_text" => "🎁 هدية ترحيبية للطلاب",
        "title_white" => "أول مرة تطلب للمرحلة؟",
        "title_gradient" => "إليك خصم إضافي 5%",
        "desc" => "اطلب بـ 300 جنيه أو أكثر، واستخدم كود الخصم التالي في سلة المشتريات لتحصل على الخصم فوراً.",
        "link" => "#",
        "btn_text" => "نسخ الكود",
        "type" => "promo",
        "promo_code" => "UNI_START_2026",
        "bg_gradient" => "from-slate-950 via-purple-950 to-slate-950",
        "image" => "https://store.ehabona.com/web/image/product.product/75/image_1024/%D8%A7%D9%84%D8%A8%D8%A7%D9%83%D8%AF%D8%AC%20%D8%A7%D9%84%D8%B7%D8%A8%D9%8A%20%D8%A7%D9%84%D9%85%D8%AA%D9%83%D8%A7%D9%85%D9%84:%20Vital%20Signs%20%26%20Suture%20Bundle?unique=a94a61d",
        "image_position" => "left",
        "active" => false
      ],
      [
        "id" => 3,
        "badge_icon" => "fa-stethoscope",
        "badge_text" => "🩺 باقات كليات الطبية",
        "title_white" => "وفر حتى 15% عند شراء",
        "title_gradient" => "الحقيبة الطبية المتكاملة",
        "desc" => "الباقة تحتوي على البالطو القطني، سماعة الطبيب، وجهاز ضغط الدم، بالإضافة إلى طقم التشريح المعقم.",
        "link" => "/shop/medical",
        "btn_text" => "اكتشف الباقات الطبية",
        "type" => "standard",
        "bg_gradient" => "from-slate-950 via-emerald-950 to-slate-950",
        "image" => "https://i.postimg.cc/TPhmzS5v/1.jpg",
        "image_position" => "right",
        "active" => false
      ]
    ];

    $heroIcons = [
      ['fa-microscope', 'from-blue-500/20 to-cyan-500/20 text-cyan-400', __('app.home_hero_card_microscopes')],
      ['fa-vial', 'from-purple-500/20 to-indigo-500/20 text-purple-400', __('app.home_hero_card_glassware')],
      ['fa-stethoscope', 'from-emerald-500/20 to-teal-500/20 text-emerald-400', __('app.home_hero_card_medical')],
      ['fa-screwdriver-wrench', 'from-amber-500/20 to-orange-500/20 text-amber-400', __('app.home_hero_card_engineering')],
    ];
  @endphp

  <div id="hero-slider"
    class="relative w-full overflow-hidden rounded-2xl bg-neutral-950 my-8 shadow-2xl border border-neutral-900">

    <div class="relative min-h-[550px] md:min-h-[500px] flex items-center">

      @foreach($slides as $index => $slide)
        <div
          class="slider-slide {{ $slide['active'] ? 'flex' : 'hidden' }} w-full min-h-[550px] md:min-h-[500px] grid grid-cols-1 md:grid-cols-2 gap-8 items-center px-8 md:px-20 py-16 transition-all duration-500 ease-in-out bg-gradient-to-r {{ $slide['bg_gradient'] }}"
          dir="rtl">

          <div
            class="flex flex-col items-center md:items-start text-center md:text-right space-y-6 {{ $slide['image_position'] == 'left' ? 'md:order-2' : 'md:order-1' }}">
            <span
              class="inline-flex items-center gap-2 px-5 py-2 rounded-full text-xs font-semibold bg-red-500/10 border border-red-500/20 text-red-400 backdrop-blur-sm">
              <i class="fas {{ $slide['badge_icon'] }}"></i>
              {{ $slide['badge_text'] }}
            </span>

            <h2 class="text-4xl md:text-6xl font-extrabold text-white tracking-tight leading-tight">
              {{ $slide['title_white'] }}
              <span
                class="block text-3xl md:text-5xl mt-3 bg-gradient-to-l from-white via-neutral-200 to-neutral-400 bg-clip-text text-transparent opacity-90">
                {{ $slide['title_gradient'] }}
              </span>
            </h2>

            <p class="text-base md:text-lg text-neutral-400 max-w-xl leading-relaxed">
              {{ $slide['desc'] }}
            </p>

            <div class="pt-4">
              @if($slide['type'] == 'promo')
                <div
                  class="flex items-center gap-2 bg-neutral-900/80 border border-purple-500/30 rounded-xl p-2 backdrop-blur-sm">
                  <span
                    class="px-4 font-mono text-purple-400 tracking-wider font-bold text-lg">{{ $slide['promo_code'] }}</span>
                  <button onclick="navigator.clipboard.writeText('{{ $slide['promo_code'] }}')"
                    class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-semibold transition-all">
                    {{ $slide['btn_text'] }}
                  </button>
                </div>
              @else
                <a href="{{ $slide['link'] }}"
                  class="inline-flex items-center justify-center px-10 py-4 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-bold rounded-xl shadow-lg shadow-red-500/20 transform hover:-translate-y-0.5 transition-all duration-200 text-base">
                  {{ $slide['btn_text'] }}
                </a>
              @endif
            </div>
          </div>

          <div
            class="flex justify-center items-center relative {{ $slide['image_position'] == 'left' ? 'md:order-1' : 'md:order-2' }}">
            <div class="absolute w-80 h-80 bg-red-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <img src="{{ asset($slide['image']) }}" alt="{{ $slide['title_white'] }}"
              class="relative max-h-[350px] md:max-h-[420px] object-contain drop-shadow-[0_0_50px_rgba(239,68,68,0.25)] transform hover:scale-105 transition-transform duration-500">
          </div>

        </div>
      @endforeach

    </div>

    <button id="prev-btn"
      class="absolute left-6 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-neutral-900/60 hover:bg-neutral-800 text-white flex items-center justify-center border border-neutral-800 backdrop-blur-sm transition-all z-10">
      <i class="fas fa-chevron-left text-base"></i>
    </button>
    <button id="next-btn"
      class="absolute right-6 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-neutral-900/60 hover:bg-neutral-800 text-white flex items-center justify-center border border-neutral-800 backdrop-blur-sm transition-all z-10">
      <i class="fas fa-chevron-right text-base"></i>
    </button>

    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-2 z-10">
      @foreach($slides as $index => $slide)
        <button
          class="slider-dot h-2.5 rounded-full transition-all duration-300 {{ $slide['active'] ? 'w-8 bg-emerald-500' : 'w-2.5 bg-neutral-600' }}"
          onclick="goToSlide({{ $index }})"></button>
      @endforeach
    </div>
  </div>

  <script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slider-slide');
    const dots = document.querySelectorAll('.slider-dot');

    function showSlide(index) {
      if (index >= slides.length) currentSlide = 0;
      else if (index < 0) currentSlide = slides.length - 1;
      else currentSlide = index;

      slides.forEach((slide, i) => {
        if (i === currentSlide) {
          slide.classList.remove('hidden');
          slide.classList.add('flex');
        } else {
          slide.classList.remove('flex');
          slide.classList.add('hidden');
        }
      });

      dots.forEach((dot, i) => {
        if (i === currentSlide) {
          dot.classList.remove('w-2.5', 'bg-neutral-600');
          dot.classList.add('w-8', 'bg-emerald-500');
        } else {
          dot.classList.add('w-2.5', 'bg-neutral-600');
          dot.classList.remove('w-8', 'bg-emerald-500');
        }
      });
    }

    document.getElementById('next-btn').addEventListener('click', () => showSlide(currentSlide + 1));
    document.getElementById('prev-btn').addEventListener('click', () => showSlide(currentSlide - 1));

    function goToSlide(index) {
      showSlide(index);
    }

    setInterval(() => {
      showSlide(currentSlide + 1);
    }, 5000);
  </script>


  {{-- ═══════════════ COLLEGE TILES (BIG) ═══════════════ --}}
  @if($mainCategories->isNotEmpty())
    <section class="py-20 bg-gradient-to-b from-slate-50 to-white relative overflow-hidden">
      <div class="max-w-[1850px] mx-auto px-6 relative z-10 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <div>
            <span
              class="inline-flex items-center gap-2 bg-violet-100 text-violet-700 border border-violet-200 rounded-full px-5 py-2 text-sm font-medium mb-4">
              🏛️ {{ __('app.home_college_tiles_eyebrow') }}
            </span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 tracking-tight mb-4">
              {{ __('app.home_college_tiles_title') }}
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
              Premium tools and instruments tailored for each academic discipline
            </p>
          </div>

          <div class="hidden md:flex justify-between items-center w-full mt-6">
            <button type="button" aria-label="Previous slide"
              class="college-prev w-11 h-11 rounded-full bg-white shadow hover:bg-blue-600 hover:text-white transition">
              <i class="fa-solid fa-chevron-left"></i>
            </button>

            <button type="button" aria-label="Next slide"
              class="college-next w-11 h-11 rounded-full bg-white shadow hover:bg-blue-600 hover:text-white transition">
              <i class="fa-solid fa-chevron-right"></i>
            </button>
          </div>
        </div>

        <!-- Swiper -->
        <div class="swiper collegeSwiper pb-12">
          <div class="swiper-wrapper">
            @foreach($mainCategories as $cat)
              <div class="swiper-slide !w-[320px]">
                <a href="{{ route('category.show', $cat->slug) }}"
                  class="group relative block aspect-[4/3] rounded-3xl overflow-hidden shadow-md hover:shadow-2xl transition-all hover:-translate-y-1"
                  style="background:linear-gradient(135deg,{{ $cat->primary_color ?? '#6366f1' }},{{ $cat->secondary_color ?? '#8b5cf6' }});">
                  @if($cat->image)
                    <img src="{{ asset('storage/' . $cat->image) }}" alt="{{ $cat->name }}"
                      class="absolute inset-0 w-full h-full object-cover opacity-40 group-hover:opacity-60 group-hover:scale-110 transition-all duration-500"
                      loading="lazy">
                  @endif
                  <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                  <div class="absolute inset-0 p-5 flex flex-col justify-between text-white">
                    <div
                      class="inline-flex w-11 h-11 rounded-xl bg-white/20 backdrop-blur items-center justify-center text-lg">
                      <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                      <h3 class="text-lg sm:text-xl font-black leading-tight">{{ $cat->name }}</h3>
                      @if($cat->children_count)
                        <p class="text-xs text-white/80 mt-1">{{ $cat->children_count }} {{ __('app.shared_departments') }}</p>
                      @endif
                      <span
                        class="inline-flex items-center gap-1.5 text-xs font-bold mt-3 bg-white/20 backdrop-blur px-3 py-1.5 rounded-full">
                        {{ __('app.home_college_tiles_shop_now') }}
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                      </span>
                    </div>
                  </div>
                </a>
              </div>
            @endforeach
          </div>

          <!-- Pagination مع تنسيق أفضل -->
          <div class="swiper-pagination mt-20 !flex !justify-center gap-3"></div>
        </div>
      </div>
    </section>
  @endif

  {{-- ═══════════════ NEW ARRIVALS ═══════════════ --}}
  @if($products->isNotEmpty())
    <section class="py-12 bg-white">
      <div class="max-w-[1850px] mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-end justify-between mb-6">
          <div>
            <span
              class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-700 px-5 py-2 rounded-full text-sm font-semibold mb-3">{{ __('app.home_new_eyebrow') }}</span>
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">{{ __('app.home_new_title') }}</h2>
          </div>

          <div class="flex flex-col items-center md:items-end gap-3">
            <a href="{{ route('products.index', ['sort' => 'newest']) }}"
              class="group inline-flex items-center justify-center bg-gradient-to-r from-sky-800 to-blue-600 hover:opacity-90 transition shadow-md shadow-violet-500/30 text-white rounded-2xl text-sm font-semibold h-12 px-8 gap-3 transition-all hover:-translate-y-0.5 shadow-lg shadow-violet-500/30">
              {{ __('app.shared_view_all') }} <svg xmlns="http://www.w3.org/2000/svg"
                class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor"
                stroke-width="3" viewBox="0 0 24 24">
                <path d="M5 12h14" stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="m12 5 7 7-7 7" stroke-linecap="round" stroke-linejoin="round"></path>
              </svg>
            </a>

            <div class="flex items-center gap-2">
              <button type="button" aria-label="Previous slide"
                class="new-prev hidden md:flex w-10 h-10 items-center justify-center rounded-full border bg-white hover:bg-blue-600 hover:text-white transition">
                <i class="fa-solid fa-chevron-left"></i>
              </button>

              <button type="button" aria-label="Next slide"
                class="new-next hidden md:flex w-10 h-10 items-center justify-center rounded-full border bg-white hover:bg-blue-600 hover:text-white transition">
                <i class="fa-solid fa-chevron-right"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="swiper newProductsSwiper">
          <div class="swiper-wrapper">

            @foreach($products->take(10) as $product)
              <div class="swiper-slide">
                @include('components.product-card', ['product' => $product])
              </div>
            @endforeach

          </div>
        </div>

      </div>
    </section>


  @endif


  {{-- ═══════════════ FEATURED / TOP PICKS ═══════════════ --}}
  @if($featuredProducts->isNotEmpty())
    <section id="featured" class="bg-white py-16 overflow-hidden">
      <div class="max-w-[1850px] mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between mb-10">
          <div>
            <span
              class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 px-5 py-2 rounded-full text-sm font-semibold mb-3">
              {{ __('app.home_featured_eyebrow') }}
            </span>

            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">
              {{ __('app.home_featured_title') }}
            </h2>

            @if(!empty($homeSections['featured_subtitle']))
              <p class="text-md mt-3 text-gray-600 max-w-md">
                {{ $homeSections['featured_subtitle'] }}
              </p>
            @endif
          </div>

          <div class="hidden md:flex gap-3">
            <button type="button" aria-label="Previous slide"
              class="featured-prev w-11 h-11 rounded-full bg-white shadow hover:bg-blue-600 hover:text-white transition">
              <i class="fa-solid fa-chevron-left"></i>
            </button>

            <button type="button" aria-label="Next slide"
              class="featured-next w-11 h-11 rounded-full bg-white shadow hover:bg-blue-600 hover:text-white transition">
              <i class="fa-solid fa-chevron-right"></i>
            </button>
          </div>
        </div>

        <!-- Swiper -->
        <div class="swiper featuredSwiper pb-12">
          <div class="swiper-wrapper">
            @foreach($featuredProducts as $product)
              <div class="swiper-slide !w-[260px]">
                @include('components.product-card', ['product' => $product])
              </div>
            @endforeach
          </div>

          <!-- Pagination تحت الكروت -->
          <div class="swiper-pagination mt-16"></div>
        </div>

      </div>
    </section>
  @endif


  {{-- ═══════════════ WHY US ═══════════════ --}}
  <section class="py-14 bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 text-white">
    <div class="max-w-[1850px] mx-auto px-4 sm:px-6 lg:px-8">

      <div class="text-center mb-10">
        <span class="text-xs font-bold uppercase tracking-wider text-cyan-400">{{ __('app.home_why_eyebrow') }}</span>
        <h2 class="text-2xl sm:text-4xl font-black mt-1 text-slate-100">{{ __('app.home_why_title') }}</h2>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @php
          $why = [
            ['fa-flask-vial', __('app.home_why_1_title'), __('app.home_why_1_desc')],
            ['fa-tags', __('app.home_why_2_title'), __('app.home_why_2_desc')],
            ['fa-truck', __('app.home_why_3_title'), __('app.home_why_3_desc')],
            ['fa-shield-halved', __('app.home_why_4_title'), __('app.home_why_4_desc')],
            ['fa-people-group', __('app.home_why_5_title'), __('app.home_why_5_desc')],
            ['fa-headset', __('app.home_why_6_title'), __('app.home_why_6_desc')],
          ];
        @endphp

        @foreach($why as [$ic, $t, $d])
          <div
            class="group bg-slate-900/40 backdrop-blur border border-slate-800 rounded-2xl p-6 hover:bg-slate-800/60 hover:border-cyan-500/30 transition-all duration-300">

            <div
              class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-400 to-sky-500 text-slate-950 flex items-center justify-center text-xl mb-4 shadow-lg shadow-cyan-500/10 group-hover:shadow-cyan-500/30 group-hover:scale-105 transition-all duration-300">
              <i class="fa-solid {{ $ic }}"></i>
            </div>

            <h3 class="font-black text-lg mb-2 text-white group-hover:text-cyan-400 transition-colors duration-200">{{ $t }}
            </h3>
            <p class="text-sm text-sky-100/70 leading-relaxed">{{ $d }}</p>

          </div>
        @endforeach
      </div>

    </div>
  </section>


  {{-- ═══════════════ BEST DEALS — TABS LINK TO FILTERED CATALOG ا ═══════════════ --}}
  @if($products->isNotEmpty())
    <section class="py-12 bg-white">
      <div class="max-w-[1850px] mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-6">
          <!-- الـ Eyebrow بالأزرق الصريح -->
          <span class="text-xs font-bold uppercase tracking-wider text-blue-600">{{ __('app.home_deals_eyebrow') }}</span>
          <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">
            {{ __('app.home_deals_title') }}
          </h2>

          @if($mainCategories->isNotEmpty())
            <div class="flex flex-wrap justify-center gap-2 mt-5">

              <button type="button"
                class="filter-btn active-filter px-5 py-2.5 rounded-full text-xs font-bold text-white bg-gradient-to-r from-sky-600 to-blue-700 shadow-md shadow-blue-500/30 border border-transparent transition-all duration-200"
                data-college="">
                {{ __('app.shared_all_products') }}
              </button>

              @foreach($mainCategories->take(6) as $c)
                <button type="button"
                  class="filter-btn px-5 py-2.5 rounded-full text-xs font-bold bg-slate-50 text-slate-600 border border-slate-200/80 hover:border-blue-500 hover:text-blue-600 hover:bg-blue-50/50 transition-all duration-200"
                  data-college="{{ $c->slug }}">
                  {{ $c->name }}
                </button>
              @endforeach

            </div>
          @endif
        </div>

        <div id="products-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">

          @foreach($products as $product)
            @php
              $productCategory = $product->category;
              $productCollegeSlug = $productCategory?->parent?->slug ?? $productCategory?->slug;
              $productCategorySlugs = collect([$productCollegeSlug, $productCategory?->slug])->filter()->unique()->implode(' ');
            @endphp

            <div class="product-item" data-colleges="{{ $productCategorySlugs }}">

              @include('components.product-card', ['product' => $product])

            </div>

          @endforeach

        </div>

        <div class="flex justify-center mt-8">
          <a href="{{ route('products.index') }}"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-full shadow-lg shadow-blue-500/20 hover:shadow-blue-600/30 transition-all duration-300 hover:-translate-y-0.5">
            {{ __('app.home_deals_browse_all') }}
            <i class="fa-solid fa-arrow-right"></i>
          </a>
        </div>

      </div>
    </section>

    <script>
      (function () {
        const buttons = document.querySelectorAll('.filter-btn');
        const items = document.querySelectorAll('#products-grid .product-item');

        // الألوان النشطة: أزرق صريح مع التدرج والظل المميز للزر النشط
        const activeCls = ['bg-gradient-to-r', 'from-sky-600', 'to-blue-700', 'text-white', 'shadow-md', 'shadow-blue-500/30', 'border-transparent', 'active-filter'];

        // الألوان العادية: تأثير الهوفر بالأزرق الصريح والخلفية الناعمة
        const idleCls = ['bg-slate-50', 'text-slate-600', 'border', 'border-slate-200/80', 'hover:border-blue-500', 'hover:text-blue-600', 'hover:bg-blue-50/50', 'transition-all', 'duration-200'];

        function setActive(btn) {
          buttons.forEach(b => {
            b.classList.remove(...activeCls);
            b.classList.add(...idleCls);
          });
          btn.classList.remove(...idleCls);
          btn.classList.add(...activeCls);
        }

        buttons.forEach(btn => {
          btn.addEventListener('click', () => {
            const slug = btn.dataset.college || '';
            setActive(btn);
            items.forEach(item => {
              const itemSlugs = (item.dataset.colleges || item.dataset.college || '').split(/\s+/).filter(Boolean);
              item.style.display = (!slug || itemSlugs.includes(slug)) ? '' : 'none';
            });
          });
        });
      })();
    </script>


  @endif


  <!-- {{-- ═══════════════ COLLEGES (CATEGORY CIRCLES) ═══════════════ --}}
                  @if($mainCategories->isNotEmpty())
                    <section id="colleges" class="bg-slate-50 py-10 overflow-hidden">
                      <div class="max-w-[1850px] mx-auto px-4 sm:px-6 lg:px-8">

                        <div class="flex items-end justify-between mb-6">
                          <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-violet-600">
                              {{ __('app.home_colleges_eyebrow') }}
                            </span>

                            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">
                              {{ __('app.home_colleges_title') }}
                            </h2>
                          </div>

                          <div class="hidden md:flex gap-3">
                            <button type="button" aria-label="Previous slide"
                              class="college-icons-prev w-11 h-11 rounded-full bg-white shadow hover:bg-violet-600 hover:text-white transition">
                              <i class="fa-solid fa-chevron-left"></i>
                            </button>

                            <button type="button" aria-label="Next slide"
                              class="college-icons-next w-11 h-11 rounded-full bg-white shadow hover:bg-violet-600 hover:text-white transition">
                              <i class="fa-solid fa-chevron-right"></i>
                            </button>
                          </div>
                        </div>

                        <div class="swiper collegeIconsSwiper">

                          <div class="swiper-wrapper">

                            @foreach($mainCategories as $cat)

                              <div class="swiper-slide !w-[150px]">

                                <a href="{{ route('category.show', $cat->slug) }}" class="group flex flex-col items-center gap-2.5">

                                  <div
                                    class="relative w-full aspect-square rounded-2xl bg-white border border-slate-200 group-hover:border-violet-400 transition-all duration-300 p-2 flex items-center justify-center overflow-hidden shadow-sm group-hover:shadow-lg group-hover:-translate-y-1">

                                    @if($cat->image)

                                      <img src="{{ asset('storage/' . $cat->image) }}" alt="{{ $cat->name }}" loading="lazy"
                                        class="w-full h-full object-contain"
                                        onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex';">

                                      <div class="hidden absolute inset-0 items-center justify-center text-2xl text-white"
                                        style="background:linear-gradient(135deg,
                                                                                                         {{ $cat->primary_color ?? '#6366f1' }},
                                                                                                         {{ $cat->secondary_color ?? '#8b5cf6' }});">

                                        <i class="fa-solid fa-graduation-cap"></i>

                                      </div>

                                    @else

                                      <div class="w-full h-full rounded-xl flex items-center justify-center text-3xl text-white"
                                        style="background:linear-gradient(135deg,
                                                                                                         {{ $cat->primary_color ?? '#6366f1' }},
                                                                                                         {{ $cat->secondary_color ?? '#8b5cf6' }});">

                                        <i class="fa-solid fa-graduation-cap"></i>

                                      </div>

                                    @endif

                                  </div>

                                  <span
                                    class="text-xs font-bold text-slate-700 group-hover:text-violet-700 text-center leading-tight line-clamp-2">
                                    {{ $cat->name }}
                                  </span>

                                </a>

                              </div>

                            @endforeach

                          </div>

                        </div>

                      </div>
                    </section>
                  @endif   -->


  {{-- ═══════════════ FEATURES STRIP ═══════════════ --}}
  <section class="bg-gradient-to-b from-white to-slate-50/50 border-b border-slate-100 py-8">
    <div class="max-w-[1850px] mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      @php
        $features = [
          ['fa-truck-fast', __('app.home_features_fast_title'), __('app.home_features_fast_sub')],
          ['fa-shield-halved', __('app.home_features_original_title'), __('app.home_features_original_sub')],
          ['fa-rotate-left', __('app.home_features_returns_title'), __('app.home_features_returns_sub')],
          ['fa-headset', __('app.home_features_support_title'), __('app.home_features_support_sub')],
        ];
      @endphp

      @foreach($features as [$ic, $title, $sub])
        <div
          class="group flex items-center gap-4 p-4 rounded-2xl bg-white border border-slate-100 shadow-sm transition-all duration-300 hover:shadow-md hover:border-blue-200/80 hover:-translate-y-0.5">

          <div
            class="w-14 h-14 shrink-0 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl transition-all duration-300 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white group-hover:shadow-lg group-hover:shadow-blue-600/20">
            <i class="fa-solid {{ $ic }}"></i>
          </div>

          <div class="min-w-0 flex-1">
            <h3
              class="font-bold text-slate-800 text-sm tracking-tight mb-0.5 group-hover:text-blue-600 transition-colors duration-200 truncate">
              {{ $title }}
            </h3>
            <p class="text-xs text-slate-500 font-medium leading-relaxed truncate">
              {{ $sub }}
            </p>
          </div>

        </div>
      @endforeach
    </div>
  </section>






@endsection