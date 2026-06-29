@php
    $fields = [
        ['name' => 'site_logo',             'label' => __('app.admin_settings_images_logo_label'),    'remove' => __('app.admin_settings_images_logo_remove'),    'icon' => 'fa-image',        'hint' => 'PNG / SVG — يفضل خلفية شفافة'],
        ['name' => 'hero_background',       'label' => __('app.admin_settings_images_hero_label'),    'remove' => __('app.admin_settings_images_hero_remove'),    'icon' => 'fa-panorama',     'hint' => 'JPG / WEBP — 1920x1080 موصى به'],
        ['name' => 'default_product_image', 'label' => __('app.admin_settings_images_product_label'), 'remove' => __('app.admin_settings_images_product_remove'), 'icon' => 'fa-box-open',     'hint' => 'JPG / PNG / WEBP — مربعة'],
        ['name' => 'default_og_image',      'label' => __('app.admin_settings_images_og_label'),      'remove' => __('app.admin_settings_images_og_remove'),      'icon' => 'fa-share-nodes',  'hint' => 'JPG / PNG — 1200x630'],
    ];
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
@foreach($fields as $f)
    @php $url = site_setting_url($f['name']); @endphp
    <div class="border border-gray-200 dark:border-gray-700 rounded-2xl p-4 bg-white dark:bg-dark-900">
        <div class="flex items-center gap-2 mb-3">
            <i class="fas {{ $f['icon'] }} text-primary-600"></i>
            <h3 class="font-bold text-sm text-gray-800 dark:text-gray-100">{{ $f['label'] }}</h3>
        </div>

        <div class="setting-img-uploader" data-field="{{ $f['name'] }}">
            <label for="setting-img-{{ $f['name'] }}"
                   class="border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-2xl p-6 text-center block cursor-pointer hover:border-primary-500 transition-colors">
                <i class="fas fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
                <p class="font-bold text-gray-700 dark:text-gray-200 text-sm">اسحب الصورة هنا أو اضغط للاختيار</p>
                <p class="text-xs text-gray-400 mt-1">{{ $f['hint'] }} — حد أقصى 4MB</p>
            </label>
            <input id="setting-img-{{ $f['name'] }}" type="file" name="{{ $f['name'] }}" accept="image/*" class="hidden setting-img-input">

            <div class="setting-img-preview mt-3 hidden">
                <div class="relative inline-block">
                    <img src="" alt="" class="h-32 w-auto max-w-full object-contain bg-gray-50 dark:bg-dark-800 rounded-xl border border-gray-200 dark:border-gray-700 p-2">
                    <button type="button" class="setting-img-clear absolute -top-2 -end-2 w-7 h-7 bg-rose-600 hover:bg-rose-700 text-white rounded-full text-xs shadow-lg">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            @if($url)
            <div class="mt-3">
                <p class="text-xs font-bold text-gray-500 mb-2">الصورة الحالية</p>
                <label class="relative inline-block border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
                    <img src="{{ $url }}" alt="" class="h-32 w-auto max-w-full object-contain bg-white dark:bg-dark-800 p-2">
                    <div class="p-2 text-xs flex items-center gap-1.5 bg-white dark:bg-dark-800 border-t border-gray-200 dark:border-gray-700">
                        <input type="checkbox" name="remove_{{ $f['name'] }}" value="1" class="accent-rose-600">
                        <span class="text-rose-600 font-medium">{{ $f['remove'] }}</span>
                    </div>
                </label>
            </div>
            @endif
        </div>
    </div>
@endforeach
</div>

@push('scripts')
<script>
document.querySelectorAll('.setting-img-uploader').forEach(box => {
    const input = box.querySelector('.setting-img-input');
    const preview = box.querySelector('.setting-img-preview');
    const img = preview.querySelector('img');
    const clearBtn = preview.querySelector('.setting-img-clear');
    const dropzone = box.querySelector('label[for]');

    input.addEventListener('change', e => {
        const file = e.target.files[0];
        if (!file) { preview.classList.add('hidden'); return; }
        img.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
    });
    clearBtn.addEventListener('click', () => {
        input.value = '';
        preview.classList.add('hidden');
    });
    ['dragover','dragenter'].forEach(ev => dropzone.addEventListener(ev, e => {
        e.preventDefault(); dropzone.classList.add('border-primary-500','bg-primary-50');
    }));
    ['dragleave','drop'].forEach(ev => dropzone.addEventListener(ev, e => {
        e.preventDefault(); dropzone.classList.remove('border-primary-500','bg-primary-50');
    }));
    dropzone.addEventListener('drop', e => {
        if (e.dataTransfer.files.length) {
            input.files = e.dataTransfer.files;
            input.dispatchEvent(new Event('change'));
        }
    });
});
</script>
@endpush
