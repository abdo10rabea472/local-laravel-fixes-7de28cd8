<?php

use App\Models\SiteSetting;

if (! function_exists('site_setting')) {
    function site_setting(string $key, ?string $default = null): ?string
    {
        return SiteSetting::get($key, $default);
    }
}

if (! function_exists('site_setting_url')) {
    function site_setting_url(string $key, ?string $default = null): ?string
    {
        return SiteSetting::getUrl($key, $default);
    }
}
