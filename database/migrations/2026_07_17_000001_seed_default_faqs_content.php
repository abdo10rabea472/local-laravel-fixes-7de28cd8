<?php

use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'faqs'],
            [
                'title'           => 'Frequently Asked Questions',
                'seo_title'       => 'FAQs | UNI-LAB MARKET',
                'seo_description' => 'Find quick answers about ordering lab equipment, shipping, returns, and more.',
                'status'          => true,
                'sort_order'      => 1,
            ]
        );

        if (trim((string) $page->content) === '') {
            $email = SiteSetting::get('contact_email') ?: 'ahmedkhamis@gmail.com';

            $faqs = [
                ['category' => 'Shipping', 'q' => 'How long does delivery take?',                  'a' => 'Standard delivery within Cairo and Giza takes 2–4 business days. Delivery to other governorates usually takes 4–7 business days.'],
                ['category' => 'Shipping', 'q' => 'Do you ship to universities and laboratories?', 'a' => 'Yes! We offer special shipping and invoicing services for universities, research centers, and educational institutions across Egypt.'],
                ['category' => 'Payment',  'q' => 'What payment methods do you accept?',           'a' => 'We accept Vodafone Cash, Fawry, credit/debit cards, and Cash on Delivery through secure encrypted gateways.'],
                ['category' => 'Warranty', 'q' => 'Do your products come with warranty?',          'a' => 'Most laboratory equipment comes with 1 to 3 years manufacturer warranty. Glassware and consumables are covered for manufacturing defects only.'],
                ['category' => 'Returns',  'q' => 'Can I return an item if I changed my mind?',    'a' => 'Yes, you can return most items within 30 days if they are in original unused condition with all packaging.'],
                ['category' => 'Payment',  'q' => 'Are the prices inclusive of VAT?',              'a' => 'All prices shown include VAT (14%). No additional tax will be added at checkout.'],
                ['category' => 'Support',  'q' => 'Do you provide technical support after purchase?', 'a' => 'Our technical team helps with installation, calibration, and usage via phone, WhatsApp, and email.'],
                ['category' => 'Orders',   'q' => 'Can I get a quote for bulk orders?',            'a' => 'Yes. Send requirements to ' . $email . ' and we will reply within 24 hours.'],
            ];

            $page->update(['content' => json_encode($faqs, JSON_UNESCAPED_UNICODE)]);
        }
    }

    public function down(): void
    {
        // Non-destructive: leave content as-is on rollback.
    }
};
