<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Product;
use App\Models\Category;
use Carbon\Carbon;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap.';

    public function handle()
    {
        $this->info('Generating sitemaps...');

        // Generate main sitemap
        $this->generateMainSitemap();

        // Generate products sitemap
        $this->generateProductsSitemap();

        // Generate categories sitemap
        $this->generateCategoriesSitemap();

        // Generate sitemap index
        $this->generateSitemapIndex();

        $this->info('All sitemaps generated successfully!');
    }

    protected function generateMainSitemap()
    {
        $this->info('Generating main sitemap...');

        $sitemap = Sitemap::create()
            ->add(Url::create('/')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0))
            ->add(Url::create('/shop')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.9))
            ->add(Url::create('/about')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.7))
            ->add(Url::create('/contact')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.7));

        $sitemap->writeToFile(public_path('sitemap.xml'));
    }

    protected function generateProductsSitemap()
    {
        $this->info('Generating products sitemap...');

        $sitemap = Sitemap::create();

        Product::where('is_active', true)->chunk(100, function ($products) use ($sitemap) {
            foreach ($products as $product) {
                $sitemap->add(
                    Url::create("/products/{$product->slug}")
                        ->setLastModificationDate($product->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.8)
                );
            }
        });

        $sitemap->writeToFile(public_path('products-sitemap.xml'));
    }

    protected function generateCategoriesSitemap()
    {
        $this->info('Generating categories sitemap...');

        $sitemap = Sitemap::create();

        Category::chunk(100, function ($categories) use ($sitemap) {
            foreach ($categories as $category) {
                $sitemap->add(
                    Url::create("/categories/{$category->slug}")
                        ->setLastModificationDate($category->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.8)
                );
            }
        });

        $sitemap->writeToFile(public_path('categories-sitemap.xml'));
    }

    protected function generateSitemapIndex()
    {
        $this->info('Generating sitemap index...');

        $sitemapIndex = Sitemap::create();

        $sitemapIndex->add('/sitemap.xml');
        $sitemapIndex->add('/products-sitemap.xml');
        $sitemapIndex->add('/categories-sitemap.xml');

        $sitemapIndex->writeToFile(public_path('sitemap_index.xml'));
    }
}
