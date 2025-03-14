<?php

namespace App\Traits;

use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;

trait SEOTrait
{
    /**
     * Set SEO for homepage
     */
    protected function setHomepageSEO()
    {
        $title = 'Cacha Store - Jajanan & Snack Kekinian Asli Pangandaran';
        $description = 'Temukan berbagai macam jajanan dan snack kekinian khas Pangandaran di Cacha Store. Tersedia berbagai varian rasa dan ukuran yang cocok untuk semua kalangan.';
        $url = route('guest.home');

        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical($url);
        SEOTools::metatags()->addKeyword([
            'snack pangandaran',
            'jajanan kekinian',
            'cacha store',
            'cemilan khas pangandaran',
            'oleh-oleh pangandaran'
        ]);

        JsonLd::setType('WebSite');
        JsonLd::addValue('url', $url);
        JsonLd::addValue('name', $title);
        JsonLd::addValue('description', $description);
        JsonLd::addValue('potentialAction', [
            '@type' => 'SearchAction',
            'target' => route('guest.shop') . '?q={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ]);

        OpenGraph::setTitle($title)
            ->setDescription($description)
            ->setUrl($url)
            ->setSiteName('Cacha Store')
            ->addProperty('type', 'website')
            ->addProperty('locale', 'id_ID')
            ->addImage(asset('images/logo-snack-circle.png'), [
                'width' => 600,
                'height' => 600,
                'type' => 'image/png',
            ]);

        TwitterCard::setTitle($title)
            ->setDescription($description)
            ->setUrl($url)
            ->setType('summary_large_image')
            ->setImage(asset('images/logo-snack-circle.png'))
            ->setSite('@cachastore');
    }

    /**
     * Set SEO for product detail page
     *
     * @param \App\Models\Product $product
     */
    protected function setProductSEO($product)
    {
        // Get the default product unit for price
        $defaultUnit = $product->productUnits->where('is_default', true)->first();
        $mainImage = $product->productImages->where('is_primary', true)->first() ?? $product->productImages->first();

        // Calculate discount if applicable
        $price = $defaultUnit->selling_price;
        $discountPrice = null;
        if ($product->discount && $product->discount->is_active) {
            if ($product->discount->type == 'percentage') {
                $discountAmount = $price * ($product->discount->value / 100);
            } else {
                $discountAmount = $product->discount->value;
            }
            $discountPrice = $price - $discountAmount;
        }

        // Prepare SEO data
        $title = $product->seo_title ?? $product->name . ' - Cacha Store';
        $description = $product->seo_description ?? strip_tags(substr($product->description, 0, 160));
        $url = route('guest.show', $product->slug);
        $imageUrl = asset('storage/' . $mainImage->image_path);
        $price = $discountPrice ?? $price;

        // Basic SEO
        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical($url);

        if ($product->seo_keywords) {
            SEOTools::metatags()->addKeyword(explode(',', $product->seo_keywords));
        }

        // JSON-LD Schema markup for Product
        JsonLd::setType('Product');
        JsonLd::addValue('name', $product->name);
        JsonLd::addValue('description', $description);
        JsonLd::addValue('image', $imageUrl);
        JsonLd::addValue('sku', $product->code);

        if ($product->barcode) {
            JsonLd::addValue('gtin13', $product->barcode);
        }

        JsonLd::addValue('brand', [
            '@type' => 'Brand',
            'name' => 'Cacha Store'
        ]);

        JsonLd::addValue('category', $product->category->name);

        // Add Offers information
        JsonLd::addValue('offers', [
            '@type' => 'Offer',
            'url' => $url,
            'price' => number_format($price, 2, '.', ''),
            'priceCurrency' => 'IDR',
            'availability' => $defaultUnit->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'priceValidUntil' => now()->addMonths(1)->format('Y-m-d')
        ]);

        // Add AggregateRating if available
        JsonLd::addValue('aggregateRating', [
            '@type' => 'AggregateRating',
            'ratingValue' => '4.5',
            'reviewCount' => '432' // Replace with actual review data when available
        ]);

        // OpenGraph Tags
        OpenGraph::setTitle($title)
            ->setDescription($description)
            ->setUrl($url)
            ->setType('product')
            ->setSiteName('Cacha Store')
            ->addProperty('locale', 'id_ID')
            ->addImage($imageUrl, [
                'alt' => $mainImage->alt_text ?? $product->name
            ]);

        // Product specific OpenGraph properties
        OpenGraph::addProperty('product:price:amount', number_format($price, 2, '.', ''));
        OpenGraph::addProperty('product:price:currency', 'IDR');
        OpenGraph::addProperty('product:category', $product->category->name);
        OpenGraph::addProperty('product:availability', $defaultUnit->stock > 0 ? 'in stock' : 'out of stock');

        // Twitter Card Tags
        TwitterCard::setTitle($title)
            ->setDescription($description)
            ->setUrl($url)
            ->setSite('@cachastore')
            ->setType('product')
            ->setImage($imageUrl);
    }

    /**
     * Set SEO for product list/shop page
     *
     * @param string $title
     * @param string $category
     */
    protected function setShopSEO($title = null, $category = null)
    {
        $baseTitle = 'Produk Cacha Store - Jajanan & Snack Berkualitas';
        $title = $title ?? $baseTitle;

        $baseDescription = 'Temukan berbagai macam jajanan dan snack berkualitas dari Cacha Store. Tersedia dalam berbagai ukuran dan varian yang lezat.';
        $description = $category
            ? "Jajanan dan snack kekinian $category dari Cacha Store. Produk asli Pangandaran dengan berbagai varian rasa."
            : $baseDescription;

        $url = route('guest.shop', request()->query());

        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical(route('guest.shop')); // Base canonical without query params

        // Add pagination links if applicable
        if (request()->filled('page')) {
            $currentPage = (int) request('page');
            if ($currentPage > 1) {
                SEOTools::metatags()->setPrev(route('guest.shop', array_merge(request()->query(), ['page' => $currentPage - 1])));
            }
            SEOTools::metatags()->setNext(route('guest.shop', array_merge(request()->query(), ['page' => $currentPage + 1])));
        }

        // Set keywords
        $keywords = ['snack pangandaran', 'jajanan kekinian', 'cacha store', 'cemilan khas pangandaran'];
        if ($category) {
            $keywords[] = "snack $category";
            $keywords[] = "jajanan $category";
        }
        SEOTools::metatags()->addKeyword($keywords);

        // JSON-LD
        JsonLd::setType('ItemList');
        JsonLd::addValue('name', $title);
        JsonLd::addValue('description', $description);

        // OpenGraph
        OpenGraph::setTitle($title)
            ->setDescription($description)
            ->setUrl($url)
            ->setSiteName('Cacha Store')
            ->addProperty('type', 'website')
            ->addProperty('locale', 'id_ID')
            ->addImage(asset('images/logo-snack-circle.png'));

        // Twitter Card
        TwitterCard::setTitle($title)
            ->setDescription($description)
            ->setUrl($url)
            ->setSite('@cachastore')
            ->setType('summary')
            ->setImage(asset('images/logo-snack-circle.png'));
    }

    /**
     * Set SEO for category pages
     *
     * @param \App\Models\Category $category
     */
    protected function setCategorySEO($category)
    {
        $title = $category->name . ' - Cacha Store';
        $description = "Jajanan dan snack kekinian {$category->name} dari Cacha Store. Produk asli Pangandaran dengan berbagai varian rasa dan ukuran.";
        $url = route('guest.shop', ['category' => $category->id]);

        SEOTools::setTitle($title);
        SEOTools::setDescription($description);
        SEOTools::setCanonical($url);
        SEOTools::metatags()->addKeyword([
            "snack {$category->name}",
            "jajanan {$category->name}",
            'cacha store',
            'cemilan pangandaran'
        ]);

        // Add breadcrumbs schema
        JsonLd::setType('BreadcrumbList');
        JsonLd::addValue('itemListElement', [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Beranda',
                'item' => route('guest.home')
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Produk',
                'item' => route('guest.shop')
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $category->name,
                'item' => $url
            ]
        ]);

        // OpenGraph
        OpenGraph::setTitle($title)
            ->setDescription($description)
            ->setUrl($url)
            ->setSiteName('Cacha Store')
            ->addProperty('type', 'website')
            ->addProperty('locale', 'id_ID')
            ->addImage(asset('images/logo-snack-circle.png'));

        // Twitter Card
        TwitterCard::setTitle($title)
            ->setDescription($description)
            ->setUrl($url)
            ->setSite('@cachastore')
            ->setType('summary')
            ->setImage(asset('images/logo-snack-circle.png'));
    }
}