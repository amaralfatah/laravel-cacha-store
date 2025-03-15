<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Group;
use App\Models\Transaction;
use App\Traits\SEOTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Artesaos\SEOTools\Facades\SEOTools;

class GuestController extends Controller
{
    use SEOTrait;

    public function getProductsByGroup()
    {
        $products = Product::whereHas('category.group', function ($query) {
            $query->where('code', 'CACHASNACK');
        })
            ->with(['category', 'productImages', 'productUnits'])
            ->where('is_active', true)
            ->get();

        return $products;
    }

    public function getCategory()
    {
        $categories = Category::whereHas('group', function ($query) {
            $query->where('code', 'CACHASNACK');
        })
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(4)
            ->get();

        return $categories;
    }

    public function productBestseller()
    {
        $bestSellers = Product::whereHas('category.group', function ($query) {
            $query->where('code', 'CACHASNACK');
        })
            ->withCount([
                'transactionItems as total_sold' => function ($query) {
                    $query->whereHas('transaction', function ($q) {
                        $q->where('status', 'success');
                    });
                }
            ])
            ->with([
                'category',
                'productImages' => function ($query) {
                    $query->where('is_primary', true);
                },
                'productUnits'
            ])
            ->orderBy('total_sold', 'desc')
            ->take(4)
            ->get();

        return $bestSellers;
    }

    public function catalogProducts()
    {
        $catalogProducts = Product::whereHas('category.group', function ($query) {
            $query->where('code', 'CACHASNACK');
        })
            ->with([
                'category',
                'productImages' => function ($query) {
                    $query->where('is_primary', true);
                },
                'productUnits'
            ])
            ->where('is_active', true)
            ->take(6)
            ->get();

        return $catalogProducts;
    }

    public function statisticData()
    {
        $statistics = [
            'product_variants' => Product::whereHas('category.group', function ($query) {
                $query->where('code', 'CACHASNACK');
            })->count(),
            'satisfied_customers' => Transaction::where('status', 'success')
                ->distinct('customer_id')
                ->count(),
            'total_cities' => 150, // Static value as per view template
            'marketplace_rating' => 4.9 // Static value as per view template
        ];

        return $statistics;
    }

    public function galleryImages()
    {
        // Ambil dulu ID produk bestseller
        $bestsellerIds = Product::whereHas('category.group', function ($query) {
            $query->where('code', 'CACHASNACK');
        })
            ->join('transaction_items', 'products.id', '=', 'transaction_items.product_id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.status', 'success')
            ->groupBy('products.id')
            ->orderByRaw('COUNT(transaction_items.id) DESC')
            ->take(4)
            ->pluck('products.id');

        // Ambil gambar produk random selain bestseller
        $gallery = ProductImage::whereHas('product.category.group', function ($query) {
            $query->where('code', 'CACHASNACK');
        })
            ->whereHas('product', function ($query) use ($bestsellerIds) {
                $query->where('is_active', true)
                    ->whereNotIn('id', $bestsellerIds);
            })
            ->with(['product:id,name,short_description,slug']) // Ensure slug is included for links
            ->inRandomOrder()
            ->distinct('product_id')
            ->take(6)
            ->get();

        return $gallery;
    }

    public function getBiggestDiscount()
    {
        // 1. Mendapatkan produk CACHASNACK dengan diskon aktif
        $products = Product::whereHas('category.group', function ($query) {
            $query->where('code', 'CACHASNACK');
        })
            ->whereNotNull('discount_id')
            ->with(['discount' => fn($q) => $q->where('is_active', true)])
            ->get();

        // 2. Ambil semua diskon dari produk-produk tersebut
        $discounts = $products->pluck('discount')->filter();

        // 3. Kembalikan diskon dengan value terbesar
        return $discounts->sortByDesc(function ($discount) {
            return $discount->type === 'percentage' ? $discount->value : 0;
        })->first();
    }

    private function getTotalProducts()
    {
        return Product::whereHas('category.group', function ($query) {
            $query->where('code', 'CACHASNACK');
        })
            ->where('is_active', true)
            ->count();
    }

    public function index()
    {
        // Get all required data
        $categories = $this->getCategory();
        $bestSellers = $this->productBestseller();
        $catalogProducts = $this->catalogProducts();
        $statistics = $this->statisticData();
        $gallery = $this->galleryImages();
        $biggestDiscount = $this->getBiggestDiscount();
        $totalProducts = $this->getTotalProducts();

        // Set SEO for homepage using the trait method
        $this->setHomepageSEO();

        // Compact all data for the view
        return view('welcome', compact(
            'categories',
            'bestSellers',
            'catalogProducts',
            'statistics',
            'gallery',
            'biggestDiscount',
            'totalProducts'
        ));
    }

    public function show($slug)
    {
        // Ambil data produk dengan relasi yang dibutuhkan
        $product = Product::where('slug', $slug)
            ->whereHas('category.group', function ($query) {
                $query->where('code', 'CACHASNACK');
            })
            ->with([
                'category',
                'productImages',
                'productUnits',
                'discount'
            ])
            ->firstOrFail();

        // Hitung diskon jika ada
        $defaultUnit = $product->productUnits->where('is_default', true)->first();
        if ($defaultUnit && $product->discount && $product->discount->is_active) {
            if ($product->discount->type == 'percentage') {
                $discountAmount = $defaultUnit->selling_price * ($product->discount->value / 100);
            } else {
                $discountAmount = $product->discount->value;
            }
            $discountPrice = $defaultUnit->selling_price - $discountAmount;
            $discountPercentage = round(($discountAmount / $defaultUnit->selling_price) * 100);
        }

        // Set SEO for product detail using the trait method
        $this->setProductSEO($product);

        // Data untuk view
        $data = [
            'product' => $product,
            'defaultUnit' => $defaultUnit,
            'discountPrice' => $discountPrice ?? null,
            'discountPercentage' => $discountPercentage ?? null,
            'productUnits' => $product->productUnits->sortBy('conversion_factor'),
            'mainImage' => $product->productImages->where('is_primary', true)->first()
                ?? $product->productImages->first(),
            'otherImages' => $product->productImages->where('is_primary', false)->take(4),
            'totalReviews' => 432, // Sementara hardcode
            'rating' => 4.5, // You can calculate this from actual reviews
            'reviews' => [], // Add your review data here
            'specifications' => [
                'category' => $product->category->name,
                'code' => $product->code,
                'barcode' => $product->barcode,
            ],
        ];

        return view('guest.show', $data);
    }

    public function productList(Request $request)
    {
        // Base query with joins for sorting
        $query = Product::query()
            ->select('products.*')
            ->leftJoin('product_units', function ($join) {
                $join->on('products.id', '=', 'product_units.product_id')
                    ->where('product_units.is_default', '=', true);
            })
            ->whereHas('category.group', function ($query) {
                $query->where('code', 'CACHASNACK');
            })
            ->with(['category', 'productImages', 'productUnits', 'discount']);

        // Selected category for SEO
        $selectedCategory = null;

        // Filter by category if provided and not "all"
        if ($request->has('category') && $request->category !== 'all') {
            $category = Category::find($request->category);
            if ($category) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('id', $request->category);
                });

                $selectedCategory = $category;
            }
        }

        // Filter by price range if provided
        if ($request->has('price_min') && $request->has('price_max')) {
            $query->whereHas('productUnits', function ($q) use ($request) {
                $q->where('is_default', true)
                    ->whereBetween('selling_price', [
                        $request->price_min,
                        $request->price_max
                    ]);
            });
        }

        // Sort products
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('product_units.selling_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('product_units.selling_price', 'desc');
                break;
            case 'bestseller':
                $query->withCount([
                    'transactionItems as total_sold' => function ($query) {
                        $query->whereHas('transaction', function ($q) {
                            $q->where('status', 'success');
                        });
                    }
                ])
                    ->orderBy('total_sold', 'desc');
                break;
            default:
                $query->latest('products.created_at');
        }

        $products = $query->paginate(12);

        // Set SEO for the shop page
        if ($selectedCategory) {
            $this->setCategorySEO($selectedCategory);
        } else {
            $this->setShopSEO();
        }

        // Add pagination links for SEO
        if ($products->currentPage() > 1) {
            SEOTools::metatags()->setPrev($products->previousPageUrl());
        }

        if ($products->hasMorePages()) {
            SEOTools::metatags()->setNext($products->nextPageUrl());
        }

        // Get categories with count
        $categories = Category::whereHas('group', function ($query) {
            $query->where('code', 'CACHASNACK');
        })->withCount('products')->get();

        // Get total products count for "All" category
        $totalProducts = Product::whereHas('category.group', function ($query) {
            $query->where('code', 'CACHASNACK');
        })->count();

        return view('guest.shop', compact('products', 'categories', 'totalProducts'));
    }
}
