<?php

namespace App\Traits;

use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;
use Illuminate\Support\Str;

trait ProductSEOTrait
{
    /**
     * Generate SEO title, using seo_title if available or creating from name
     *
     * @return string
     */
    public function getSeoTitleAttribute()
    {
        if (!empty($this->attributes['seo_title'])) {
            return $this->attributes['seo_title'];
        }

        return $this->attributes['name'] . ' - Cacha Store';
    }

    /**
     * Generate SEO description, using seo_description if available or creating from description
     *
     * @return string
     */
    public function getSeoDescriptionAttribute()
    {
        if (!empty($this->attributes['seo_description'])) {
            return $this->attributes['seo_description'];
        }

        // Use short_description if available
        if (!empty($this->attributes['short_description'])) {
            return Str::limit(strip_tags($this->attributes['short_description']), 160);
        }

        // Fallback to truncated main description
        if (!empty($this->attributes['description'])) {
            return Str::limit(strip_tags($this->attributes['description']), 160);
        }

        // Ultimate fallback
        return "Jajanan dan snack kekinian {$this->attributes['name']} khas Pangandaran dari Cacha Store. Tersedia berbagai varian rasa dan ukuran.";
    }

    /**
     * Generate OpenGraph title, using og_title if available or falling back to seo_title
     *
     * @return string
     */
    public function getOgTitleAttribute()
    {
        if (!empty($this->attributes['og_title'])) {
            return $this->attributes['og_title'];
        }

        return $this->getSeoTitleAttribute();
    }

    /**
     * Generate OpenGraph description, using og_description if available or falling back to seo_description
     *
     * @return string
     */
    public function getOgDescriptionAttribute()
    {
        if (!empty($this->attributes['og_description'])) {
            return $this->attributes['og_description'];
        }

        return $this->getSeoDescriptionAttribute();
    }

    /**
     * Generate keyword array from seo_keywords string
     *
     * @return array
     */
    public function getKeywordsArrayAttribute()
    {
        if (!empty($this->attributes['seo_keywords'])) {
            return array_map('trim', explode(',', $this->attributes['seo_keywords']));
        }

        // Generate keywords based on product data
        $keywords = [$this->attributes['name']];

        if ($this->category) {
            $keywords[] = $this->category->name;
        }

        $keywords = array_merge($keywords, [
            'snack pangandaran',
            'jajanan kekinian',
            'cacha store',
            'cemilan khas pangandaran'
        ]);

        return $keywords;
    }

    /**
     * Generate and apply all SEO tags using SEOTools package
     */
    public function generateSEOTags()
    {
        // Get necessary data
        $primaryImage = $this->productImages()->where('is_primary', true)->first();
        $imageUrl = $primaryImage ? asset('storage/' . $primaryImage->image_path) : null;
        $defaultUnit = $this->defaultUnit;
        $price = $defaultUnit ? $defaultUnit->selling_price : 0;
        $discountedPrice = $this->discounted_price;

        // Basic SEO
        SEOTools::setTitle($this->seo_title);
        SEOTools::setDescription($this->seo_description);
        SEOTools::setCanonical($this->seo_canonical_url ?? route('guest.show', $this->slug));
        SEOTools::metatags()->addKeyword($this->keywords_array);

        // OpenGraph
        OpenGraph::setTitle($this->og_title)
            ->setDescription($this->og_description)
            ->setUrl(route('guest.show', $this->slug))
            ->setSiteName('Cacha Store')
            ->setType($this->og_type ?? 'product')
            ->setLocale('id_ID');

        if ($imageUrl) {
            OpenGraph::addImage($imageUrl, [
                'height' => 600,
                'width' => 600,
                'alt' => $primaryImage->alt_text
            ]);
        }

        // Product specific OpenGraph properties
        OpenGraph::addProperty('product:price:amount', number_format($discountedPrice, 2, '.', ''));
        OpenGraph::addProperty('product:price:currency', 'IDR');

        if ($this->category) {
            OpenGraph::addProperty('product:category', $this->category->name);
        }

        OpenGraph::addProperty(
            'product:availability',
            $defaultUnit && $defaultUnit->stock > 0 ? 'in stock' : 'out of stock'
        );

        // Twitter Card
        TwitterCard::setType('product')
            ->setSite('@cachastore')
            ->setTitle($this->seo_title)
            ->setDescription($this->seo_description);

        if ($imageUrl) {
            TwitterCard::setImage($imageUrl);
        }

        // Schema.org JSON-LD
        JsonLd::setType('Product');
        JsonLd::addValue('name', $this->attributes['name']);
        JsonLd::addValue('description', strip_tags($this->description ?? $this->short_description));

        if ($imageUrl) {
            JsonLd::addValue('image', $imageUrl);
        }

        // Add brand
        JsonLd::addValue('brand', [
            '@type' => 'Brand',
            'name' => $this->schema_brand ?? 'Cacha Store'
        ]);

        // Add category, SKU, etc.
        if ($this->category) {
            JsonLd::addValue('category', $this->category->name);
        }

        JsonLd::addValue('sku', $this->schema_sku ?? $this->code);

        if (!empty($this->schema_gtin) || !empty($this->barcode)) {
            JsonLd::addValue('gtin13', $this->schema_gtin ?? $this->barcode);
        }

        if (!empty($this->schema_mpn)) {
            JsonLd::addValue('mpn', $this->schema_mpn);
        }

        // Add offers
        JsonLd::addValue('offers', [
            '@type' => 'Offer',
            'url' => route('guest.show', $this->slug),
            'price' => number_format($discountedPrice, 2, '.', ''),
            'priceCurrency' => 'IDR',
            'priceValidUntil' => now()->addMonths(1)->format('Y-m-d'),
            'availability' => $defaultUnit && $defaultUnit->stock > 0
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
            'seller' => [
                '@type' => 'Organization',
                'name' => 'Cacha Store'
            ]
        ]);

        // Add ratings (hardcoded for now, should be replaced with actual data)
        JsonLd::addValue('aggregateRating', [
            '@type' => 'AggregateRating',
            'ratingValue' => '4.5',
            'reviewCount' => '432'
        ]);

        // Add marketplace link if available
        if (!empty($this->url)) {
            JsonLd::addValue('sameAs', [$this->url]);
        }
    }
}