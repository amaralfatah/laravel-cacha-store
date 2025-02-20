<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Product;
use App\Models\Category;
use App\Models\Discount;
use App\Models\ProductImage;
use App\Models\StockAdjustment;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\Tax;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Milon\Barcode\DNS1D;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::select([
                'products.id',
                'products.code',
                'products.name',
                'products.barcode',
                'products.store_id',
                'products.category_id',
                'products.supplier_id',
                'products.is_active',
                'products.created_at'
            ])->with([
                'store:id,name',
                'category:id,name,group_id',
                'category.group:id,name',  // Tambahkan relasi ke group
                'supplier:id,name',
                'productUnits' => function($query) {
                    $query->where('is_default', true)
                        ->select([
                            'id',
                            'product_id',
                            'unit_id',
                            'purchase_price',
                            'selling_price',
                            'stock',
                            'min_stock'
                        ]);
                },
                'productUnits.unit:id,name'
            ]);

            // Filter berdasarkan store untuk non-admin
            if (auth()->user()->role !== 'admin') {
                $products->where('products.store_id', auth()->user()->store_id);
            }

            // Filter grup
            if ($request->filled('group_id')) {
                $products->whereHas('category', function($query) use ($request) {
                    $query->where('group_id', $request->group_id);
                });
            }

            // Filter kategori
            if ($request->filled('category_id')) {
                $products->where('category_id', $request->category_id);
            }

            // Filter supplier
            if ($request->filled('supplier_id')) {
                $products->where('supplier_id', $request->supplier_id);
            }

            // Filter status aktif
            if ($request->filled('is_active')) {
                $products->where('is_active', $request->is_active);
            }

            // Filter status stok
            if ($request->filled('stock_status')) {
                $products->whereHas('productUnits', function($query) use ($request) {
                    if ($request->stock_status === 'low') {
                        $query->whereColumn('stock', '<=', 'min_stock')
                            ->where('stock', '>', 0);
                    } elseif ($request->stock_status === 'out') {
                        $query->where('stock', '<=', 0);
                    }
                });
            }

            // Default ordering by created_at in descending order (newest first)
            $products->orderBy('products.created_at', 'desc');

            return DataTables::of($products)
                ->addColumn('store_name', function ($product) {
                    return $product->store->name ?? '-';
                })
                ->addColumn('group_info', function ($product) {
                    return $product->category?->group?->name ?? '-';
                })
                ->addColumn('category_name', function ($product) {
                    return $product->category?->name ?? '-';
                })
                ->addColumn('supplier_name', function ($product) {
                    return $product->supplier?->name ?? '-';
                })
                ->addColumn('unit_info', function ($product) {
                    $defaultUnit = $product->productUnits->first();
                    if (!$defaultUnit) return '-';

                    return $defaultUnit->unit->name;
                })
                ->addColumn('stock_info', function ($product) {
                    $defaultUnit = $product->productUnits->first();
                    if (!$defaultUnit) return '-';

                    $stockStatus = '';
                    if ($defaultUnit->stock <= 0) {
                        $stockStatus = '<span class="badge bg-danger">Habis</span>';
                    } elseif ($defaultUnit->stock <= $defaultUnit->min_stock) {
                        $stockStatus = '<span class="badge bg-warning">Menipis</span>';
                    }

                    return sprintf(
                        '%s %s %s',
                        number_format($defaultUnit->stock),
                        $defaultUnit->unit->name,
                        $stockStatus
                    );
                })
                ->addColumn('purchase_price', function ($product) {
                    $defaultUnit = $product->productUnits->first();
                    return $defaultUnit ? 'Rp' . number_format($defaultUnit->purchase_price) : '-';
                })
                ->addColumn('selling_price', function ($product) {
                    $defaultUnit = $product->productUnits->first();
                    return $defaultUnit ? 'Rp' . number_format($defaultUnit->selling_price) : '-';
                })
                ->addColumn('status', function ($product) {
                    return $product->is_active ?
                        '<span class="badge bg-success">Aktif</span>' :
                        '<span class="badge bg-danger">Nonaktif</span>';
                })
                ->addColumn('action', function ($product) {
                    return view('products.partials.action-buttons', compact('product'))->render();
                })
                ->rawColumns(['stock_info', 'price_info', 'status', 'action'])
                ->make(true);
        }

        $groups = Group::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function($query) {
                return $query->where('store_id', auth()->user()->store_id);
            })
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $categories = Category::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function($query) {
                return $query->where('store_id', auth()->user()->store_id);
            })
            ->select('id', 'name', 'group_id')
            ->orderBy('name')
            ->get();

        $suppliers = Supplier::when(auth()->user()->role !== 'admin', function($query) {
            return $query->where('store_id', auth()->user()->store_id);
        })
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('products.index', compact('groups', 'categories', 'suppliers'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function($query) {
                return $query->where('store_id', auth()->user()->store_id);
            })
            ->get();

        $units = Unit::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function($query) {
                return $query->where('store_id', auth()->user()->store_id);
            })
            ->get();

        $stores = auth()->user()->role === 'admin'
            ? \App\Models\Store::all()
            : \App\Models\Store::where('id', auth()->user()->store_id)->get();

        return view('products.create', compact('categories', 'units', 'stores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'code' => 'required|unique:products|max:255',
            'barcode' => 'required|unique:products|max:100',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'default_unit_id' => 'required|exists:units,id',
            'stock' => 'required|numeric|min:0',
            'store_id' => auth()->user()->role === 'admin' ? 'required|exists:stores,id' : '',
            'is_active' => 'boolean',
            'featured' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'url' => 'nullable|url'
        ]);

        try {
            DB::beginTransaction();

            // Generate barcode
            $barcode = new DNS1D();
            $barcode->setStorPath(storage_path('app/public/barcodes'));
            $barcodeImage = $barcode->getBarcodePNG($validated['barcode'], 'C128');
            $barcodePath = 'barcodes/' . $validated['barcode'] . '.png';
            Storage::disk('public')->put($barcodePath, base64_decode($barcodeImage));

            // Get store and category data for SEO
            $store = Store::find($validated['store_id'] ?? auth()->user()->store_id);
            $category = Category::find($validated['category_id']);

            // Generate SEO fields
            $seoData = $this->generateSeoData(
                $validated['name'],
                $validated['short_description'],
                $validated['description'],
                $store,
                $category
            );

            // Create product with slug and SEO data
            $product = Product::create([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'],
                'short_description' => $validated['short_description'],
                'barcode' => $validated['barcode'],
                'barcode_image' => $barcodePath,
                'category_id' => $validated['category_id'],
                'store_id' => auth()->user()->role === 'admin'
                    ? $validated['store_id']
                    : auth()->user()->store_id,
                'featured' => $request->has('featured'),
                'is_active' => $request->has('is_active'),

                // SEO fields
                'seo_title' => $seoData['seo_title'],
                'seo_description' => $seoData['seo_description'],
                'seo_keywords' => $seoData['seo_keywords'],
                'seo_canonical_url' => $seoData['seo_canonical_url'],

                // OpenGraph fields
                'og_title' => $seoData['og_title'],
                'og_description' => $seoData['og_description'],
                'og_type' => 'product',

                // Schema.org fields
                'schema_brand' => $store->name,
                'schema_sku' => $validated['code'],
                'schema_gtin' => $validated['barcode'],
                'schema_mpn' => $validated['code'],

                'url' => $validated['url']
            ]);

            // Create product unit
            $product->productUnits()->create([
                'store_id' => $product->store_id,
                'unit_id' => $validated['default_unit_id'],
                'conversion_factor' => 1,
                'purchase_price' => $validated['purchase_price'],
                'selling_price' => $validated['selling_price'],
                'stock' => $validated['stock'],
                'is_default' => true
            ]);

            // Handle multiple images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');

                    $product->images()->create([
                        'store_id' => $product->store_id,
                        'image_path' => $path,
                        'is_primary' => $index === 0,
                        'sort_order' => $index,
                        'alt_text' => $validated['name'] . ' - ' . ($index + 1)
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('products.index')
                ->with('success', 'Produk Berhasil Dibuat');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Gagal membuat produk: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Generate SEO data from product information
     */
    private function generateSeoData($name, $shortDescription, $description, $store, $category)
    {
        // Clean and limit text for SEO
        $cleanDescription = strip_tags($description ?? '');
        $productName = ucwords(strtolower($name));

        // Base URL for canonical
        $baseUrl = url('/products');

        return [
            // SEO - Title max 60 chars
            'seo_title' => Str::limit($productName . ' - ' . $store->name, 60),

            // SEO - Description max 160 chars
            'seo_description' => Str::limit(
                $shortDescription ?? $cleanDescription ?? $productName,
                160
            ),

            // SEO - Keywords
            'seo_keywords' => implode(', ', [
                $productName,
                $category->name,
                $store->name,
                // Additional relevant keywords
                'beli ' . strtolower($productName),
                'jual ' . strtolower($productName)
            ]),

            // SEO - Canonical URL
            'seo_canonical_url' => $baseUrl . '/' . Str::slug($name),

            // OpenGraph - Title max 95 chars
            'og_title' => Str::limit($productName . ' | ' . $store->name, 95),

            // OpenGraph - Description max 200 chars
            'og_description' => Str::limit(
                $shortDescription ?? $cleanDescription ?? $productName,
                200
            )
        ];
    }

    public function edit(Product $product)
    {
        // Access check
        if (auth()->user()->role !== 'admin' &&
            $product->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        // Load product with its images
        $product->load(['images' => function($query) {
            $query->orderBy('is_primary', 'desc')
                ->orderBy('sort_order', 'asc');
        }]);

        $categories = Category::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function($query) {
                return $query->where('store_id', auth()->user()->store_id);
            })
            ->get();

        $taxes = Tax::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function($query) {
                return $query->where('store_id', auth()->user()->store_id);
            })
            ->get();

        $discounts = Discount::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function($query) {
                return $query->where('store_id', auth()->user()->store_id);
            })
            ->get();

        $stores = auth()->user()->role === 'admin'
            ? \App\Models\Store::all()
            : \App\Models\Store::where('id', auth()->user()->store_id)->get();

        $defaultUnit = $product->productUnits()->where('is_default', true)->first();

        return view('products.edit', compact(
            'product',
            'categories',
            'taxes',
            'discounts',
            'defaultUnit',
            'stores'
        ));
    }

    public function update(Request $request, Product $product)
    {
        if (auth()->user()->role !== 'admin' &&
            $product->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|max:255',
            'code' => 'required|max:255|unique:products,code,' . $product->id,
            'barcode' => 'required|max:100|unique:products,barcode,' . $product->id,
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'tax_id' => 'nullable|exists:taxes,id',
            'discount_id' => 'nullable|exists:discounts,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'store_id' => auth()->user()->role === 'admin' ? 'required|exists:stores,id' : '',
            'is_active' => 'boolean',
            'featured' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'url' => 'nullable|url'
        ]);

        try {
            DB::beginTransaction();

            // Get store for SEO data
            $store = Store::find($validated['store_id'] ?? $product->store_id);
            $category = Category::find($validated['category_id']);

            // Generate SEO data
            $seoData = $this->generateSeoData(
                $validated['name'],
                $validated['short_description'],
                $validated['description'],
                $store,
                $category
            );

            // Update product
            $product->update([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'],
                'short_description' => $validated['short_description'],
                'barcode' => $validated['barcode'],
                'category_id' => $validated['category_id'],
                'tax_id' => $validated['tax_id'],
                'discount_id' => $validated['discount_id'],
                'store_id' => auth()->user()->role === 'admin'
                    ? $validated['store_id']
                    : $product->store_id,
                'featured' => $request->has('featured'),
                'is_active' => $request->has('is_active'),

                // SEO fields
                'seo_title' => $seoData['seo_title'],
                'seo_description' => $seoData['seo_description'],
                'seo_keywords' => $seoData['seo_keywords'],
                'seo_canonical_url' => $seoData['seo_canonical_url'],

                // OpenGraph fields
                'og_title' => $seoData['og_title'],
                'og_description' => $seoData['og_description'],

                'url' => $validated['url']
            ]);

            // Update default unit
            $defaultUnit = $product->productUnits()->where('is_default', true)->first();
            if ($defaultUnit) {
                $defaultUnit->update([
                    'purchase_price' => $validated['purchase_price'],
                    'selling_price' => $validated['selling_price'],
                    'stock' => $validated['stock']
                ]);

                // Update other units based on conversion factor
                $otherUnits = $product->productUnits()
                    ->where('id', '!=', $defaultUnit->id)
                    ->get();

                foreach ($otherUnits as $unit) {
                    $unit->update([
                        'purchase_price' => $validated['purchase_price'] * $unit->conversion_factor,
                        'selling_price' => $validated['selling_price'] * $unit->conversion_factor,
                        'stock' => floor($validated['stock'] / $unit->conversion_factor)
                    ]);
                }
            }

            // Handle new images
            if ($request->hasFile('images')) {
                $hasPrimaryImage = $product->images()->where('is_primary', true)->exists();

                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');

                    $product->images()->create([
                        'store_id' => $product->store_id,
                        'image_path' => $path,
                        'is_primary' => !$hasPrimaryImage && $index === 0,
                        'sort_order' => $product->images->count() + $index,
                        'alt_text' => $validated['name'] . ' - ' . ($index + 1)
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Failed to update product: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Generate SEO data from product information
     */
//    private function generateSeoData($name, $shortDescription, $description, $store, $category)
//    {
//        // Clean and limit text for SEO
//        $cleanDescription = strip_tags($description ?? '');
//        $productName = ucwords(strtolower($name));
//
//        // Base URL for canonical
//        $baseUrl = url('/products');
//
//        return [
//            // SEO - Title max 60 chars
//            'seo_title' => Str::limit($productName . ' - ' . $store->name, 60),
//
//            // SEO - Description max 160 chars
//            'seo_description' => Str::limit(
//                $shortDescription ?? $cleanDescription ?? $productName,
//                160
//            ),
//
//            // SEO - Keywords
//            'seo_keywords' => implode(', ', [
//                $productName,
//                $category->name,
//                $store->name,
//                'beli ' . strtolower($productName),
//                'jual ' . strtolower($productName)
//            ]),
//
//            // SEO - Canonical URL
//            'seo_canonical_url' => $baseUrl . '/' . Str::slug($name),
//
//            // OpenGraph - Title max 95 chars
//            'og_title' => Str::limit($productName . ' | ' . $store->name, 95),
//
//            // OpenGraph - Description max 200 chars
//            'og_description' => Str::limit(
//                $shortDescription ?? $cleanDescription ?? $productName,
//                200
//            )
//        ];
//    }

    public function show(Product $product)
    {
        // Access check
        if (auth()->user()->role !== 'admin' &&
            $product->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        // Load product with all necessary relations
        $product->load([
            'store:id,name,code',
            'category:id,name,code,group_id',
            'category.group:id,name',
            'supplier:id,name,code,phone',
            'tax:id,name,rate',
            'discount:id,name,type,value',
            'images' => fn($q) => $q->orderBy('is_primary', 'desc')->orderBy('sort_order'),
            'productUnits' => fn($q) => $q->orderBy('is_default', 'desc')->with([
                'unit:id,name,code',
                'prices' => fn($q) => $q->orderBy('min_quantity')
            ]),
            'transactionItems' => fn($q) => $q->with([
                'transaction:id,invoice_number,invoice_date,status',
                'unit:id,code'
            ])->latest()->take(5),
            'stockHistories' => fn($q) => $q->with([
                'productUnit.unit:id,code'
            ])->latest()->take(20)
        ]);

        // Get default unit for quick access
        $defaultUnit = $product->productUnits->where('is_default', true)->first();

        // Calculate statistics
        $statistics = [
            'total_sales' => $product->transactionItems
                ->where('transaction.status', 'success')
                ->sum('quantity'),
            'total_revenue' => $product->transactionItems
                ->where('transaction.status', 'success')
                ->sum('subtotal'),
            'stock_value' => $product->productUnits->sum(
                fn($unit) => $unit->stock * $unit->purchase_price
            ),
            'potential_revenue' => $product->productUnits->sum(
                fn($unit) => $unit->stock * $unit->selling_price
            ),
            'average_margin' => $defaultUnit ?
                (($defaultUnit->selling_price - $defaultUnit->purchase_price) / $defaultUnit->purchase_price) * 100 : 0
        ];

        // Get recent stock adjustments
        $stockAdjustments = StockAdjustment::query()
            ->with('user:id,name')
            ->whereIn('product_unit_id', $product->productUnits->pluck('id'))
            ->latest()
            ->take(10)
            ->get();

        return view('products.show', compact(
            'product',
            'defaultUnit',
            'statistics',
            'stockAdjustments'
        ));
    }

    public function destroy(Product $product)
    {
        if (auth()->user()->role !== 'admin' &&
            $product->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            // Delete product units and related data
            $product->productUnits()->delete();
            $product->images()->delete();
            $product->prices()->delete();

            // Delete barcode image if exists
            if ($product->barcode_image) {
                Storage::disk('public')->delete($product->barcode_image);
            }

            // Delete product images
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }

            // Finally delete the product
            $product->delete();

            DB::commit();
            return redirect()
                ->route('products.index')
                ->with('success', 'Product has been deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    public function storeImages(Request $request, Product $product)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('images')) {
                // Check if product has primary image
                $hasPrimaryImage = $product->images()->where('is_primary', true)->exists();

                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');

                    $product->images()->create([
                        'store_id' => $product->store_id,
                        'image_path' => $path,
                        'is_primary' => !$hasPrimaryImage && $index === 0, // First image is primary if no primary exists
                        'sort_order' => $product->images->count() + $index,
                        'alt_text' => $product->name
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', 'Images uploaded successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to upload images: ' . $e->getMessage());
        }
    }

    public function deleteImage($id)
    {
        $image = ProductImage::findOrFail($id);

        // Access check
        if (auth()->user()->role !== 'admin' &&
            $image->product->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            // Delete the physical file
            Storage::disk('public')->delete($image->image_path);

            // If this was primary image, make the next image primary
            if ($image->is_primary) {
                $nextImage = $image->product->images()
                    ->where('id', '!=', $image->id)
                    ->orderBy('sort_order')
                    ->first();

                if ($nextImage) {
                    $nextImage->update(['is_primary' => true]);
                }
            }

            // Delete the record
            $image->delete();

            // Reorder remaining images
            $image->product->images()
                ->orderBy('sort_order')
                ->get()
                ->each(function ($img, $index) {
                    $img->update(['sort_order' => $index]);
                });

            DB::commit();
            return response()->json(['message' => 'Image deleted successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to delete image'], 500);
        }
    }
}
