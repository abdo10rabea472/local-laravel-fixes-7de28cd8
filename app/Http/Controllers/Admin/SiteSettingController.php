<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function __construct(private ImageService $imageService)
    {
    }

    public function index(Request $request): View
    {
        $tab = $request->get('tab', 'general');
        $allowedTabs = ['general', 'images', 'contact', 'ai'];

        if (! in_array($tab, $allowedTabs, true)) {
            $tab = 'general';
        }

        $settings = SiteSetting::orderBy('sort_order')->orderBy('id')->get()->keyBy('key');

        $meta = match ($tab) {
            'general' => ['title' => 'معلومات الموقع', 'subtitle' => 'اسم الموقع، اللون الأساسي، ورسالة الترحيب.'],
            'images' => ['title' => 'الصور', 'subtitle' => 'شعار الموقع، الخلفيات، والصور الافتراضية.'],
            'contact' => ['title' => 'معلومات التواصل', 'subtitle' => 'بيانات التواصل والعنوان.'],
            'ai' => ['title' => 'نماذج الذكاء الاصطناعي', 'subtitle' => 'أضف أي مزود AI متوافق مع OpenAI (Base URL + API Key + Model).'],
            default => ['title' => 'إعدادات الموقع', 'subtitle' => ''],
        };

        return view('admin.settings.index', [
            'settings' => $settings,
            'tab' => $tab,
            'activeTab' => $tab,
            'title' => $meta['title'],
            'subtitle' => $meta['subtitle'],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $tab = $request->get('tab', 'general');
        $imageKeys = ['site_logo', 'hero_background', 'default_product_image', 'default_og_image', 'welcome_popup_image'];

        foreach ($request->except(['_token', '_method', 'tab']) as $key => $value) {
            $setting = SiteSetting::firstOrNew(['key' => $key]);

            if ($request->hasFile($key)) {
                if ($setting->value) {
                    $this->imageService->deletePaths($setting->value);
                }
                $setting->value = $this->imageService->storeSettingImage($request->file($key), $key);
                $setting->type = 'image';
            } elseif (in_array($key, $imageKeys, true) && $request->has("remove_{$key}")) {
                if ($setting->value) {
                    $this->imageService->deletePaths($setting->value);
                }
                $setting->value = null;
            } else {
                $setting->value = $value;
            }

            $setting->label = $setting->label ?: $this->defaultLabel($key);
            $setting->group = $setting->group ?: 'general';
            $setting->save();
        }

        SiteSetting::clearCache();

        return redirect()->route('admin.settings.index', ['tab' => $tab])->with('success', 'تم حفظ الإعدادات بنجاح.');
    }

    public function testAi(Request $request)
    {
        $data = $request->validate([
            'base_url' => ['required','url'],
            'api_key'  => ['required','string'],
            'model'    => ['required','string'],
        ]);

        $base = rtrim($data['base_url'], '/');
        $endpoint = str_ends_with($base, '/chat/completions') ? $base : $base.'/chat/completions';

        try {
            $resp = \Illuminate\Support\Facades\Http::withToken($data['api_key'])
                ->acceptJson()
                ->timeout(20)
                ->post($endpoint, [
                    'model' => $data['model'],
                    'messages' => [['role' => 'user', 'content' => 'ping']],
                    'max_tokens' => 5,
                ]);

            if ($resp->successful()) {
                $reply = data_get($resp->json(), 'choices.0.message.content', '');
                return response()->json([
                    'ok' => true,
                    'message' => 'تم الاتصال بنجاح ✅',
                    'reply' => is_string($reply) ? mb_substr($reply, 0, 200) : '',
                ]);
            }

            return response()->json([
                'ok' => false,
                'message' => 'فشل الاتصال (HTTP '.$resp->status().')',
                'error' => data_get($resp->json(), 'error.message', $resp->body()),
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'خطأ أثناء الاتصال',
                'error' => $e->getMessage(),
            ], 200);
        }
    }

    private function defaultLabel(string $key): string
    {
        return match ($key) {
            'site_name' => 'اسم الموقع',
            'primary_color' => 'اللون الأساسي',
            'welcome_message' => 'رسالة الترحيب',
            'site_logo' => 'شعار الموقع',
            'hero_background' => 'خلفية الصفحة الرئيسية',
            'default_product_image' => 'صورة المنتج الافتراضية',
            'default_og_image' => 'صورة Open Graph الافتراضية',
            'contact_email' => 'البريد الإلكتروني للتواصل',
            'contact_phone' => 'رقم الهاتف',
            'contact_address' => 'العنوان',
            'order_id_prefix' => 'بادئة رقم الطلب',
            'welcome_popup_enabled' => 'تفعيل نموذج الترحيب',
            'welcome_popup_title' => 'عنوان نموذج الترحيب',
            'welcome_popup_message' => 'رسالة الترحيب',
            'welcome_popup_discount_code' => 'كود خصم الترحيب',
            'welcome_popup_discount_percent' => 'نسبة خصم الترحيب',
            'welcome_popup_button_text' => 'نص زر الترحيب',
            'welcome_popup_image' => 'صورة نموذج الترحيب',
            'ai_provider_name' => 'اسم مزود AI',
            'ai_base_url' => 'رابط الـ API (Base URL)',
            'ai_api_key' => 'مفتاح الـ API',
            'ai_model' => 'اسم النموذج',
            'ai_enabled' => 'تفعيل الذكاء الاصطناعي',
            default => $key,
        };
    }
}
