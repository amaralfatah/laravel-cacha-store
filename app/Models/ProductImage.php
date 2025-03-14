<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductImage extends Model
{
    protected $fillable = [
        'store_id',
        'product_id',
        'image_path',
        'is_primary',
        'sort_order',
        'alt_text'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-generate alt text if not provided
        static::creating(function ($image) {
            if (empty($image->alt_text)) {
                $product = Product::find($image->product_id);
                if ($product) {
                    // Generate descriptive alt text with SEO value
                    $alt = $product->name;
                    if ($image->is_primary) {
                        $alt .= ' - Produk Utama Cacha Store';
                    } else {
                        $alt .= ' - Cacha Store';
                    }
                    $image->alt_text = $alt;
                }
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Make sure alt_text always returns a value for SEO purposes
     * Returns a descriptive fallback if no alt text is set
     *
     * @param string|null $value
     * @return string
     */
    public function getAltTextAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        // Jika alt_text kosong di database, gunakan nama produk
        if ($this->product) {
            $description = $this->is_primary
                ? 'Produk Utama'
                : 'Varian Produk';

            return "{$this->product->name} - {$description} Cacha Store";
        }

        return 'Produk Cacha Store - Jajanan dan Snack Kekinian Asli Pangandaran';
    }

    /**
     * Get the complete image URL
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (empty($this->image_path)) {
            return asset('images/placeholder.png');
        }

        // Check if the path starts with http or https (external URL)
        if (Str::startsWith($this->image_path, ['http://', 'https://'])) {
            return $this->image_path;
        }

        // Check if the path starts with 'images/' (not stored in storage)
        if (Str::startsWith($this->image_path, 'images/')) {
            return asset($this->image_path);
        }

        // Handle path that begins with 'payne/' (theme assets)
        if (Str::startsWith($this->image_path, 'payne/')) {
            return asset($this->image_path);
        }

        // Default: image is in storage
        return asset('storage/' . $this->image_path);
    }

    /**
     * Get size information for structured data in image objects
     *
     * @return array
     */
    public function getSizeDataAttribute()
    {
        return [
            'width' => 600, // Default for structured data
            'height' => 600, // Default for structured data
        ];
    }
}