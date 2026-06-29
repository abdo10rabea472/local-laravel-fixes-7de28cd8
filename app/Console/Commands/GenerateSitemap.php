<?php

namespace App\Console\Commands;

use App\Http\Controllers\SitemapController;
use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'يكتب public/sitemap.xml و public/robots.txt بأحدث محتوى';

    public function handle(SitemapController $c): int
    {
        $sitemap = $c->index()->getContent();
        $robots  = $c->robots()->getContent();

        file_put_contents(public_path('sitemap.xml'), $sitemap);
        file_put_contents(public_path('robots.txt'),  $robots);

        $this->info('✔ تم توليد public/sitemap.xml');
        $this->info('✔ تم توليد public/robots.txt');
        return self::SUCCESS;
    }
}
