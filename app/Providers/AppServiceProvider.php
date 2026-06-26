<?php

namespace App\Providers;

use App\Services\NavigationService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer(['components.front-header', 'components.front-footer'], function ($view) {
            $nav = NavigationService::getData();

            $view->with([
                'navCategories' => $nav['colleges'],
                'navTotalProducts' => $nav['totalProducts'],
                'navTotalColleges' => $nav['totalColleges'],
                'navHeaderMenu' => $nav['headerMenu'],
                'navTopMenu' => $nav['topMenu'],
                'navFooterMenu' => $nav['footerMenu'],
            ]);
        });
    }
}
