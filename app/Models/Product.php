<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use HasSlug;

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
        'schema_mpn'
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
        return $this->hasMany(Price::class);
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

    public function getSlugOptions() : SlugOptions
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
        if (!$this->discount) {
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

    public function getSeoData()
    {
        return [
            'title' => $this->seo_title ?? $this->name,
            'description' => $this->seo_description ?? $this->short_description,
            'keywords' => $this->seo_keywords,
            'canonical' => $this->seo_canonical_url,
            'opengraph' => [
                'title' => $this->og_title ?? $this->name,
                'description' => $this->og_description ?? $this->short_description,
                'type' => $this->og_type ?? 'product',
                'image' => $this->primaryImage?->image_path ? asset('storage/' . $this->primaryImage->image_path) : null,
            ],
            'json-ld' => [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => $this->name,
                'description' => $this->description,
                'brand' => [
                    '@type' => 'Brand',
                    'name' => $this->schema_brand
                ],
                'sku' => $this->schema_sku,
                'gtin' => $this->schema_gtin,
                'mpn' => $this->schema_mpn,
                'image' => $this->primaryImage?->image_path ? asset('storage/' . $this->primaryImage->image_path) : null,
                'offers' => [
                    '@type' => 'Offer',
                    'availability' => $this->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                    'price' => $this->selling_price,
                    'priceCurrency' => 'IDR'
                ]
            ]
        ];
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
}
