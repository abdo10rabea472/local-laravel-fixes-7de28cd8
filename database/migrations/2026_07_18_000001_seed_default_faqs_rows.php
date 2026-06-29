<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('faqs')) {
            return;
        }
        if (DB::table('faqs')->count() > 0) {
            return; // don't duplicate
        }

        $email = class_exists(\App\Models\SiteSetting::class)
            ? (\App\Models\SiteSetting::get('contact_email') ?: 'ahmedkhamis@gmail.com')
            : 'ahmedkhamis@gmail.com';

        $now = now();
        $rows = [
            ['Shipping',  'How long does delivery take?',              'Standard delivery within Cairo and Giza takes 2–4 business days. Delivery to other governorates usually takes 4–7 business days.'],
            ['Shipping',  'Do you ship to universities and laboratories?', 'Yes! We offer special shipping and invoicing services for universities, research centers, and educational institutions across Egypt.'],
            ['Payment',   'What payment methods do you accept?',       'We accept Vodafone Cash, Fawry, credit/debit cards, and Cash on Delivery through secure encrypted gateways.'],
            ['Warranty',  'Do your products come with warranty?',      'Most laboratory equipment comes with 1 to 3 years manufacturer warranty. Glassware and consumables are covered for manufacturing defects only.'],
            ['Returns',   'Can I return an item if I changed my mind?', 'Yes, you can return most items within 30 days if they are in original unused condition with all packaging.'],
            ['Payment',   'Are the prices inclusive of VAT?',          'All prices shown include VAT (14%). No additional tax will be added at checkout.'],
            ['Support',   'Do you provide technical support after purchase?', 'Our technical team helps with installation, calibration, and usage via phone, WhatsApp, and email.'],
            ['Orders',    'Can I get a quote for bulk orders?',        'Yes. Send requirements to ' . $email . ' and we will reply within 24 hours.'],
        ];

        $insert = [];
        foreach ($rows as $i => [$cat, $q, $a]) {
            $insert[] = [
                'category'   => $cat,
                'question'   => $q,
                'answer'     => $a,
                'sort_order' => $i,
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('faqs')->insert($insert);
    }

    public function down(): void
    {
        // Non-destructive seed; nothing to roll back safely.
    }
};
