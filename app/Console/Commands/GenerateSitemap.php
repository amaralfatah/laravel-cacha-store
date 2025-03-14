<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap';

    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemap = Sitemap::create();

        // Add static pages
        $sitemap->add(Url::create(route('guest.home'))
            ->setLastModificationDate(Carbon::yesterday())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
            ->setPriority(1.0));

        $sitemap->add(Url::create(route('guest.shop'))
            ->setLastModificationDate(Carbon::yesterday())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
            ->setPriority(0.9));

        // Add categories
        Category::whereHas('group', function ($query) {
            $query->where('code', 'CACHASNACK');
        })
            ->chunk(100, function ($categories) use ($sitemap) {
                foreach ($categories as $category) {
                    $sitemap->add(Url::create(route('guest.shop', ['category' => $category->id]))
                        ->setLastModificationDate($category->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.8));
                }
            });

        // Add products
        Product::where('is_active', true)
            ->whereHas('category.group', function ($query) {
                $query->where('code', 'CACHASNACK');
            })
            ->chunk(100, function ($products) use ($sitemap) {
                foreach ($products as $product) {
                    $sitemap->add(Url::create(route('guest.show', $product->slug))
                        ->setLastModificationDate($product->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.7));
                }
            });

        // Save sitemap
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully!');
    }
}