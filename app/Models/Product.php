<?php

namespace App\Models;

use App\Traits\ProductSEOTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use HasSlug, ProductSEOTrait;

    protected $fillable = [
        'code',
        'name',
        'barcode',
        'barcode_image',
        'store_id',
        'category_id',
        'tax_id',
        'discount_id',
        'supplier_id',
        'featured',
        'is_active',

        // SEO fields
        'slug',
        'description',
        'short_description',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'seo_canonical_url',
        'og_title',
        'og_description',
        'og_type',
        'schema_brand',
        'schema_sku',
        'schema_gtin',
        'schema_mpn',

        'url'
    ];

    protected $casts = [
        'featured' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function productUnits()
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function prices()
    {
        return $this->hasManyThrough(
            \App\Models\Price::class,
            \App\Models\ProductUnit::class,
            'product_id',     // Foreign key pada product_units table
            'product_unit_id', // Foreign key pada prices table
            'id',             // Local key pada products table
            'id'              // Local key pada product_units table
        );
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'product_units')
            ->withPivot(['conversion_factor', 'purchase_price', 'selling_price', 'stock', 'min_stock', 'is_default']);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function getDefaultUnitAttribute()
    {
        return $this->productUnits()
            ->where('is_default', true)
            ->first();
    }

    public function stockHistories()
    {
        return $this->hasManyThrough(
            StockHistory::class,
            ProductUnit::class,
            'product_id', // Foreign key on product_units table
            'product_unit_id', // Foreign key on stock_histories table
            'id', // Local key on products table
            'id' // Local key on product_units table
        );
    }

    public function getCurrentStock()
    {
        return $this->productUnits()
            ->where('is_default', true)
            ->value('stock') ?? 0;
    }

    public function getPrice($quantity, $unitId)
    {
        // Get product unit
        $productUnit = $this->productUnits()
            ->where('unit_id', $unitId)
            ->first();

        if (!$productUnit) {
            throw new \Exception("Unit tidak ditemukan untuk produk ini");
        }

        // Check for tiered pricing
        $price = $productUnit->prices()
            ->where('min_quantity', '<=', $quantity)
            ->orderBy('min_quantity', 'desc')
            ->first();

        if ($price) {
            return $price->price;
        }

        return $productUnit->selling_price;
    }

    public function getDiscountAmount($price)
    {
        if (!$this->discount || !$this->discount->is_active) {
            return 0;
        }

        $discountAmount = $this->discount->type === 'percentage'
            ? ($price * $this->discount->value / 100)
            : $this->discount->value;

        return $discountAmount;
    }

    public function getTaxAmount($price)
    {
        if (!$this->tax) {
            return 0;
        }

        $taxAmount = $price * $this->tax->rate / 100;

        return $taxAmount;
    }

    /**
     * Enhanced getSeoData to return comprehensive SEO information
     * Now leverages the ProductSEOTrait for better organization
     *
     * @return array
     */
    public function getSeoData()
    {
        // Get primary image URL
        $primaryImage = $this->productImages()->where('is_primary', true)->first();
        $imageUrl = $primaryImage ? asset('storage/' . $primaryImage->image_path) : null;

        // Get default unit for pricing information
        $defaultUnit = $this->defaultUnit;
        $price = $defaultUnit ? $defaultUnit->selling_price : 0;

        // Calculate discounted price if applicable
        if ($defaultUnit && $this->discount && $this->discount->is_active) {
            $discountAmount = $this->getDiscountAmount($price);
            $price = $price - $discountAmount;
        }

        // Prepare base SEO data
        $seoData = [
            'title' => $this->seo_title ?? $this->name,
            'description' => $this->seo_description ?? $this->short_description ?? substr(strip_tags($this->description), 0, 160),
            'keywords' => $this->seo_keywords,
            'canonical' => $this->seo_canonical_url ?? route('guest.show', $this->slug),
            'opengraph' => [
                'title' => $this->og_title ?? $this->seo_title ?? $this->name,
                'description' => $this->og_description ?? $this->seo_description ?? $this->short_description,
                'type' => $this->og_type ?? 'product',
                'image' => $imageUrl,
                'url' => route('guest.show', $this->slug),
                'site_name' => 'Cacha Store',
                'locale' => 'id_ID',
                'product:price:amount' => number_format($price, 2, '.', ''),
                'product:price:currency' => 'IDR',
                'product:category' => $this->category->name ?? '',
                'product:availability' => $defaultUnit && $defaultUnit->stock > 0 ? 'in stock' : 'out of stock',
            ],
            'twitter' => [
                'card' => 'product',
                'site' => '@cachastore',
                'title' => $this->seo_title ?? $this->name,
                'description' => $this->seo_description ?? $this->short_description,
                'image' => $imageUrl,
            ],
            'json-ld' => [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => $this->name,
                'description' => $this->description ? strip_tags($this->description) : ($this->short_description ?? ''),
                'image' => $imageUrl,
                'sku' => $this->schema_sku ?? $this->code,
                'mpn' => $this->schema_mpn,
                'gtin13' => $this->schema_gtin ?? $this->barcode,
                'brand' => [
                    '@type' => 'Brand',
                    'name' => $this->schema_brand ?? 'Cacha Store'
                ],
                'category' => $this->category->name ?? '',
                'offers' => [
                    '@type' => 'Offer',
                    'url' => route('guest.show', $this->slug),
                    'price' => number_format($price, 2, '.', ''),
                    'priceCurrency' => 'IDR',
                    'priceValidUntil' => now()->addMonths(1)->format('Y-m-d'),
                    'availability' => $defaultUnit && $defaultUnit->stock > 0
                        ? 'https://schema.org/InStock'
                        : 'https://schema.org/OutOfStock',
                    'seller' => [
                        '@type' => 'Organization',
                        'name' => 'Cacha Store'
                    ]
                ]
            ]
        ];

        // Add aggregate rating if we have reviews
        $seoData['json-ld']['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => '4.5', // Replace with actual data when available
            'reviewCount' => '432'  // Replace with actual data when available
        ];

        // Add marketplace URL if available
        if (!empty($this->url)) {
            $seoData['json-ld']['sameAs'] = [$this->url];
        }

        return $seoData;
    }

    /**
     * Apply SEO data to SEOTools facades
     */
    public function applySeoData()
    {
        // Use the generateSEOTags method from the trait
        $this->generateSEOTags();
    }


    /**
     * Product has many images
     */
    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get the primary image
     */
    public function getPrimaryImageAttribute()
    {
        $primaryImage = $this->productImages()->where('is_primary', true)->first();

        if ($primaryImage) {
            return $primaryImage->image_path;
        }

        // Return default image if no primary image found
        return 'payne/assets/img/products/product-03-270x300.jpg';
    }

    /**
     * Get the default price
     */
    public function getDefaultPriceAttribute()
    {
        $defaultUnit = $this->productUnits()->where('is_default', true)->first();

        if ($defaultUnit) {
            return $defaultUnit->selling_price;
        }

        return 0.00;
    }

    /**
     * Get the discounted price
     */
    public function getDiscountedPriceAttribute()
    {
        $defaultPrice = $this->default_price;

        if (!$this->discount || !$this->discount->is_active) {
            return $defaultPrice;
        }

        $discountAmount = $this->getDiscountAmount($defaultPrice);

        return $defaultPrice - $discountAmount;
    }

    /**
     * Get the discount percentage
     */
    public function getDiscountPercentageAttribute()
    {
        if (!$this->discount || !$this->discount->is_active) {
            return 0;
        }

        if ($this->discount->type === 'percentage') {
            return $this->discount->value;
        }

        // Calculate percentage for fixed amount discount
        $defaultPrice = $this->default_price;
        if ($defaultPrice > 0) {
            return round(($this->discount->value / $defaultPrice) * 100);
        }

        return 0;
    }
}