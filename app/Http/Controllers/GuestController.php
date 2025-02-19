<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;

class GuestController extends Controller
{
    /**
     * Fungsi untuk filter produk berdasarkan kode kategori
     */
    private function getProductsByCategory($query, $categoryCode = 'CACHASNACK')
    {
        // Mendapatkan ID kategori dari kode kategori
        $category = Category::where('code', $categoryCode)->first();

        if ($category) {
            // Jika kategori ditemukan, filter produk berdasarkan category_id
            return $query->where('category_id', $category->id);
        }

        // Jika kategori tidak ditemukan, kembalikan query asli
        return $query;
    }

    public function index()
    {
        // Mendapatkan produk unggulan dengan kategori CACHASNACK
        $featuredProducts = $this->getProductsByCategory(
            Product::with(['productImages' => function($query) {
                $query->where('is_primary', true);
            }])
                ->where('featured', true)
                ->where('is_active', true)
        )
            ->take(3)
            ->get()
            ->map(function($product) {
                // Menetapkan gambar default jika tidak ada gambar utama
                if (!$product->productImages->first()) {
                    $product->image = asset('assets/img/products/keripik-talas-premium-349x388.png');
                } else {
                    $product->image = asset('storage/' . $product->productImages->first()->image_path);
                }
                return $product;
            });

        // Mendapatkan produk terbaru dengan kategori CACHASNACK
        $newArrivals = $this->getProductsByCategory(
            Product::with(['productImages', 'productUnits'])
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
        )
            ->take(8)
            ->get()
            ->map(function($product) {
                // Mencari unit default dan harganya
                $defaultUnit = $product->productUnits->where('is_default', true)->first();
                $price = $defaultUnit ? $defaultUnit->selling_price : 0;

                // Menetapkan gambar default jika tidak ada gambar utama
                if (!$product->productImages->where('is_primary', true)->first()) {
                    $product->image = asset('assets/img/products/keripik-singkong-pedas-270x300.jpg');
                } else {
                    $product->image = asset('storage/' . $product->productImages->where('is_primary', true)->first()->image_path);
                }

                $product->price = $price;
                return $product;
            });

        // Mendapatkan produk populer dengan kategori CACHASNACK
        $popularProducts = $this->getProductsByCategory(
            Product::with(['productImages', 'productUnits'])
                ->where('is_active', true)
                ->orderBy('featured', 'desc')
        )
            ->take(4)
            ->get()
            ->map(function($product) {
                // Mencari unit default dan harganya
                $defaultUnit = $product->productUnits->where('is_default', true)->first();
                $price = $defaultUnit ? $defaultUnit->selling_price : 0;

                // Menetapkan gambar default jika tidak ada gambar utama
                if (!$product->productImages->where('is_primary', true)->first()) {
                    $product->image = asset('assets/img/products/keripik-pisang-coklat-270x300.jpg');
                } else {
                    $product->image = asset('storage/' . $product->productImages->where('is_primary', true)->first()->image_path);
                }

                $product->price = $price;
                return $product;
            });

        // Mendapatkan produk penawaran khusus dengan kategori CACHASNACK
        $countdownProduct = $this->getProductsByCategory(
            Product::with(['productImages', 'productUnits'])
                ->where('is_active', true)
                ->where('featured', true)
        )
            ->inRandomOrder()
            ->first();

        if ($countdownProduct) {
            // Mendapatkan semua gambar untuk carousel produk
            $countdownProductImages = ProductImage::where('product_id', $countdownProduct->id)
                ->orderBy('sort_order')
                ->take(3)
                ->get();

            // Jika ada kurang dari 3 gambar, isi dengan gambar default
            $defaultImages = [
                'main' => [
                    asset('assets/img/products/keripik-singkong-balado-321x450.png'),
                    asset('assets/img/products/keripik-singkong-balado-2-321x450.png'),
                    asset('assets/img/products/keripik-singkong-balado-3-321x450.png')
                ],
                'thumbs' => [
                    asset('assets/img/products/keripik-singkong-balado-thumb-123x127.jpg'),
                    asset('assets/img/products/keripik-singkong-balado-2-thumb-123x127.jpg'),
                    asset('assets/img/products/keripik-singkong-balado-3-thumb-123x127.jpg')
                ]
            ];

            $mainImages = [];
            $thumbImages = [];

            // Gunakan gambar asli jika ada atau gunakan default
            for ($i = 0; $i < 3; $i++) {
                if (isset($countdownProductImages[$i])) {
                    $mainImages[] = asset('storage/' .$countdownProductImages[$i]->image_path);
                    $thumbImages[] = asset('storage/' . $countdownProductImages[$i]->image_path);
                } else {
                    $mainImages[] = $defaultImages['main'][$i];
                    $thumbImages[] = $defaultImages['thumbs'][$i];
                }
            }

            $countdownProduct->mainImages = $mainImages;
            $countdownProduct->thumbImages = $thumbImages;

            // Mendapatkan harga dari unit default
            $defaultUnit = $countdownProduct->productUnits->where('is_default', true)->first();
            $countdownProduct->price = $defaultUnit ? $defaultUnit->selling_price : 25000;

            // Memperbaiki format harga
            $countdownProduct->price = number_format($countdownProduct->price, 0, ',', '.');
        } else {
            // Membuat produk countdown dummy jika tidak ada
            $countdownProduct = (object)[
                'name' => 'KERIPIK SINGKONG BALADO',
                'short_description' => 'Keripik singkong premium dengan bumbu balado khas yang bikin ketagihan. Tekstur renyah sempurna dengan rasa pedas yang pas di lidah. Dikemas dengan desain modern yang instagramable.',
                'price' => '25.000',
                'mainImages' => [
                    asset('assets/img/products/keripik-singkong-balado-321x450.png'),
                    asset('assets/img/products/keripik-singkong-balado-2-321x450.png'),
                    asset('assets/img/products/keripik-singkong-balado-3-321x450.png')
                ],
                'thumbImages' => [
                    asset('assets/img/products/keripik-singkong-balado-thumb-123x127.jpg'),
                    asset('assets/img/products/keripik-singkong-balado-2-thumb-123x127.jpg'),
                    asset('assets/img/products/keripik-singkong-balado-3-thumb-123x127.jpg')
                ]
            ];
        }

        // Membuat produk promosi dengan kategori CACHASNACK
        $promotionProducts = $this->getProductsByCategory(
            Product::whereHas('productUnits', function($query) {
                // Mencari produk dengan diskon terkait
                $query->whereNotNull('discount_id');
            })
                ->with(['productImages' => function($query) {
                    $query->where('is_primary', true);
                }])
                ->where('is_active', true)
        )
            ->take(2)
            ->get()
            ->map(function($product) {
                // Menetapkan gambar default jika tidak ada gambar utama
                if (!$product->productImages->first()) {
                    $firstImage = asset('assets/img/products/keripik-talas-pedas-500x575.jpg');
                    $secondImage = asset('assets/img/products/tempe-crispy-premium-500x466.jpg');
                    // Gambar bergantian berdasarkan id produk
                    $product->image = $product->id % 2 == 0 ? $firstImage : $secondImage;
                } else {
                    $product->image = asset('storage/' . $product->productImages->first()->image_path);
                }
                return $product;
            });

        // Jika tidak ada produk promosi yang ditemukan, gunakan data dummy
        if ($promotionProducts->count() < 2) {
            $dummyPromos = [
                (object)[
                    'id' => 1,
                    'name' => 'Keripik Talas Pedas',
                    'image' => asset('payne/assets/img/products/product-14-500x575.jpg')
                ],
                (object)[
                    'id' => 2,
                    'name' => 'Tempe Crispy Premium',
                    'image' => asset('payne/assets/img/products/product-15-500x466.jpg')
                ]
            ];

            // Isi produk yang kurang dengan data dummy
            for ($i = $promotionProducts->count(); $i < 2; $i++) {
                $promotionProducts->push($dummyPromos[$i]);
            }
        }

        return view('welcome', compact(
            'featuredProducts',
            'newArrivals',
            'popularProducts',
            'countdownProduct',
            'promotionProducts'
        ));
    }

    public function shop()
    {
        // Mendapatkan semua produk snack tradisional
        $products = $this->getProductsByCategory(
            Product::with(['productImages', 'productUnits', 'category'])
                ->where('is_active', true)
        )
            ->paginate(12)
            ->through(function($product) {
                // Mencari unit default dan harganya
                $defaultUnit = $product->productUnits->where('is_default', true)->first();
                $price = $defaultUnit ? $defaultUnit->selling_price : 0;

                // Menetapkan gambar default jika tidak ada gambar utama
                if (!$product->productImages->where('is_primary', true)->first()) {
                    $product->image = asset('assets/img/products/default-snack-270x300.jpg');
                } else {
                    $product->image = asset('storage/' . $product->productImages->where('is_primary', true)->first()->image_path);
                }

                $product->price = $price;
                return $product;
            });

        // Mendapatkan semua kategori snack
        $categories = Category::where('group_id', function($query) {
            $query->select('id')
                ->from('groups')
                ->where('code', 'SNACK');
        })->where('is_active', true)->get();

        return view('guest.shop', compact('products', 'categories'));
    }

    public function productDetails($slug)
    {
        $product = Product::with([
            'productImages',
            'productUnits.unit',
            'category',
            'supplier'
        ])->where('slug', $slug)->firstOrFail();

        // Mendapatkan produk terkait
        $relatedProducts = $this->getProductsByCategory(
            Product::with(['productImages', 'productUnits'])
                ->where('is_active', true)
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
        )
            ->take(4)
            ->get()
            ->map(function($relatedProduct) {
                // Mencari unit default dan harganya
                $defaultUnit = $relatedProduct->productUnits->where('is_default', true)->first();
                $price = $defaultUnit ? $defaultUnit->selling_price : 0;

                // Menetapkan gambar default jika tidak ada gambar utama
                if (!$relatedProduct->productImages->where('is_primary', true)->first()) {
                    $relatedProduct->image = asset('assets/img/products/default-snack-270x300.jpg');
                } else {
                    $relatedProduct->image = asset('storage/' . $relatedProduct->productImages->where('is_primary', true)->first()->image_path);
                }

                $relatedProduct->price = $price;
                return $relatedProduct;
            });

        return view('guest.product-details', compact('product', 'relatedProducts'));
    }

    public function show($id)
    {
        $product = Product::with([
            'productImages',
            'productUnits.unit',
            'category'
        ])->findOrFail($id);

        // Get primary image or default image
        $primaryImage = $product->productImages->where('is_primary', true)->first();
        $image = $primaryImage
            ? asset('storage/' . $primaryImage->image_path)
            : asset('assets/img/products/default-snack-270x300.jpg');

        // Get default unit for pricing
        $defaultUnit = $product->productUnits->where('is_default', true)->first();
        $price = $defaultUnit ? $defaultUnit->selling_price : 0;

        // Check if product has discount
        $hasDiscount = $defaultUnit && $defaultUnit->discount_id;
        $discountPrice = $hasDiscount ? ($price * 0.8) : null; // Assuming 20% discount

        // Prepare variants for size selection
        $variants = $product->productUnits->map(function($unit) {
            return [
                'id' => $unit->id,
                'name' => $unit->unit->name,
                'code' => $unit->unit->code,
                'price' => $unit->selling_price,
                'is_default' => $unit->is_default
            ];
        });

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'short_description' => $product->short_description,
            'image' => $image,
            'price' => $hasDiscount ? $discountPrice : $price,
            'original_price' => $hasDiscount ? $price : null,
            'discount_price' => $discountPrice,
            'has_discount' => $hasDiscount,
            'stock' => $defaultUnit ? $defaultUnit->stock : null,
            'default_unit_id' => $defaultUnit ? $defaultUnit->id : null,
            'variants' => $variants,
            'category' => [
                'id' => $product->category->id,
                'name' => $product->category->name
            ]
        ]);
    }

    public function contactUs()
    {
        return view('guest.contact-us');
    }
}
