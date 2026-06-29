<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// تحديث public/sitemap.xml و public/robots.txt يومياً
Schedule::command('sitemap:generate')->dailyAt('03:00');
