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
                'products.is_active',
                'products.created_at'
            ])->with([
                'store:id,name',
                'category:id,name,group_id',
                'category.group:id,name',
                'productUnits' => function ($query) {
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

            // Filter grup (termasuk kategori yang berhubungan dengan grup)
            if ($request->filled('group_id')) {
                $products->whereHas('category', function ($query) use ($request) {
                    $query->where('group_id', $request->group_id);
                });
            }

            if ($request->filled('category_id')) {
                $products->where('products.category_id', $request->category_id);
            }

            // Filter status aktif
            if ($request->filled('is_active')) {
                $products->where('is_active', $request->is_active);
            }

            // Filter status stok
            if ($request->filled('stock_status')) {
                $products->whereHas('productUnits', function ($query) use ($request) {
                    if ($request->stock_status === 'low') {
                        $query->whereColumn('stock', '<=', 'min_stock')
                            ->where('stock', '>', 0);
                    } elseif ($request->stock_status === 'out') {
                        $query->where('stock', '<=', 0);
                    }
                });
            }

            // Handle column-specific filtering for barcode
            if ($request->filled('columns')) {
                foreach ($request->columns as $column) {
                    if ($column['name'] === 'barcode' && !empty($column['search']['value'])) {
                        $barcodeValue = $column['search']['value'];
                        $products->where('products.barcode', '=', $barcodeValue);
                    }
                }
            }

            if ($request->filled('search') && $request->search['value'] !== '') {
                $searchValue = $request->search['value'];
                $products->where(function ($query) use ($searchValue) {
                    $query->where('products.name', 'like', '%' . $searchValue . '%')
                        ->orWhere('products.code', 'like', '%' . $searchValue . '%')
                        ->orWhere('products.barcode', 'like', '%' . $searchValue . '%'); // Added barcode search
                });
            }

            // Default ordering by created_at in descending order (newest first)
            $products->orderBy('products.created_at', 'desc');

            return DataTables::of($products)
                ->addColumn('store_name', function ($product) {
                    return $product->store->name ?? '-';
                })
                ->addColumn('name_link', function ($product) {
                    return '<a href="' . route('products.show', $product->id) .
                        '" class="btn btn-outline-primary "
                   data-bs-toggle="tooltip" title="Klik untuk detail produk">' .
                        e($product->name) . '</a>';
                })
                ->addColumn('group_name', function ($product) {
                    return $product->category?->group?->name ?? '-';
                })
                ->addColumn('category_name', function ($product) {
                    return $product->category?->name ?? '-';
                })
                ->addColumn('stock_info', function ($product) {
                    $defaultUnit = $product->productUnits->first();
                    if (!$defaultUnit) return '-';

                    $stockStatus = '';
                    if ($defaultUnit->stock <= 0) {
                        $stockStatus = '<span class="badge badge-sm bg-danger">Habis</span>';
                    } elseif ($defaultUnit->stock <= $defaultUnit->min_stock) {
                        $stockStatus = '<span class="badge badge-sm bg-warning">Menipis</span>';
                    } else {
                        $stockStatus = '<span class="badge badge-sm bg-success">Tersedia</span>';
                    }

                    return sprintf(
                        '%s %s %s',
                        number_format($defaultUnit->stock),
                        $defaultUnit->unit->name,
                        $stockStatus
                    );
                })
                ->addColumn('selling_price', function ($product) {
                    $defaultUnit = $product->productUnits->first();
                    return $defaultUnit ? 'Rp' . number_format($defaultUnit->selling_price) : '-';
                })
                ->addColumn('status', function ($product) {
                    return $product->is_active ?
                        '<span class="badge badge-sm bg-success">Aktif</span>' :
                        '<span class="badge badge-sm bg-danger">Nonaktif</span>';
                })
                ->rawColumns(['name_link', 'stock_info', 'status'])
                ->make(true);
        }

        $groups = Group::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function ($query) {
                return $query->where('store_id', auth()->user()->store_id);
            })
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $categories = Category::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function ($query) {
                return $query->where('store_id', auth()->user()->store_id);
            })
            ->select('id', 'name', 'group_id')
            ->orderBy('name')
            ->get();

        return view('products.index', compact('groups', 'categories'));
    }

    public function create()
    {
        // Load categories with eager loading group to get code
        $categories = Category::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function ($query) {
                return $query->where('store_id', auth()->user()->store_id);
            })
            ->with('group') // Eager load group data
            ->get();

        // Get groups for the dropdown
        $groups = Group::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function ($query) {
                return $query->where('store_id', auth()->user()->store_id);
            })
            ->get();

        $units = Unit::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function ($query) {
                return $query->where('store_id', auth()->user()->store_id);
            })
            ->get();

        // Add taxes and discounts
        $taxes = Tax::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function ($query) {
                return $query->where('store_id', auth()->user()->store_id);
            })
            ->get();

        $discounts = Discount::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function ($query) {
                return $query->where('store_id', auth()->user()->store_id);
            })
            ->get();

        $stores = auth()->user()->role === 'admin'
            ? \App\Models\Store::all()
            : \App\Models\Store::where('id', auth()->user()->store_id)->get();

        return view('products.create', compact('categories', 'groups', 'units', 'stores', 'taxes', 'discounts'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|max:255',
                'code' => 'required|unique:products|max:255',
                'barcode' => 'nullable|unique:products|max:100',
                'description' => 'nullable|string',
                'short_description' => 'nullable|string|max:500',
                'category_id' => 'required|exists:categories,id',
                'purchase_price' => 'required|numeric|min:0',
                'selling_price' => 'required|numeric|min:0',
                'stock' => 'required|numeric|min:0',
                'min_stock' => 'nullable|numeric|min:0',
                'default_unit_id' => 'required|exists:units,id',
                'store_id' => auth()->user()->role === 'admin' ? 'required|exists:stores,id' : '',
                'tax_id' => 'nullable|exists:taxes,id',
                'discount_id' => 'nullable|exists:discounts,id',
                'url' => 'nullable|url',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            DB::beginTransaction();

            // Variabel untuk barcode path
            $barcodePath = null;

            // Generate barcode jika barcode diisi
            if (!empty($validated['barcode'])) {
                $barcode = new DNS1D();
                $barcode->setStorPath(storage_path('app/public/barcodes'));
                $barcodeImage = $barcode->getBarcodePNG($validated['barcode'], 'C128');
                $barcodePath = 'barcodes/' . $validated['barcode'] . '.png';
                Storage::disk('public')->put($barcodePath, base64_decode($barcodeImage));
            }

            // Get store and category data for SEO
            $store = Store::find($validated['store_id'] ?? auth()->user()->store_id);
            $category = Category::find($validated['category_id']);

            // Generate SEO fields
            $seoData = $this->generateSeoData(
                $validated['name'],
                $validated['short_description'] ?? null,
                $validated['description'] ?? null,
                $store,
                $category
            );

            // Create product with slug and SEO data
            $product = Product::create([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'] ?? null,
                'short_description' => $validated['short_description'] ?? null,
                'barcode' => $validated['barcode'],
                'barcode_image' => $barcodePath,
                'category_id' => $validated['category_id'],
                'store_id' => auth()->user()->role === 'admin'
                    ? $validated['store_id']
                    : auth()->user()->store_id,
                'tax_id' => $validated['tax_id'] ?? null,
                'discount_id' => $validated['discount_id'] ?? null,
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

                'url' => $validated['url'] ?? null
            ]);

            // Create product unit
            $productUnit = $product->productUnits()->create([
                'store_id' => $product->store_id,
                'unit_id' => $validated['default_unit_id'],
                'conversion_factor' => 1,
                'purchase_price' => $validated['purchase_price'],
                'selling_price' => $validated['selling_price'],
                'stock' => $validated['stock'],
                'min_stock' => $validated['min_stock'] ?? 0,
                'is_default' => true
            ]);

            // Add stock history entry for initial stock
            if ($validated['stock'] > 0) {
                \App\Models\StockHistory::create([
                    'store_id' => $product->store_id,
                    'product_unit_id' => $productUnit->id,
                    'reference_type' => 'initial_stock',
                    'reference_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $validated['stock'],
                    'remaining_stock' => $validated['stock'],
                    'notes' => 'Initial stock during product creation',
                    'created_at' => now()
                ]);
            }

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

            // Log the error for debugging
            \Log::error('Product creation failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

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
        if (
            auth()->user()->role !== 'admin' &&
            $product->store_id !== auth()->user()->store_id
        ) {
            abort(403);
        }

        // Load product with its images and ensure category.group is loaded
        $product->load([
            'images' => function ($query) {
                $query->orderBy('is_primary', 'desc')
                    ->orderBy('sort_order', 'asc');
            },
            'category.group'
        ]);

        $categories = Category::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function ($query) {
                return $query->where('store_id', auth()->user()->store_id);
            })
            ->with('group')
            ->get();

        // Get groups for the dropdown
        $groups = Group::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function ($query) {
                return $query->where('store_id', auth()->user()->store_id);
            })
            ->get();

        $taxes = Tax::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function ($query) {
                return $query->where('store_id', auth()->user()->store_id);
            })
            ->get();

        $discounts = Discount::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function ($query) {
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
            'groups',
            'taxes',
            'discounts',
            'defaultUnit',
            'stores'
        ));
    }

    public function update(Request $request, Product $product)
    {
        // Access check
        if (
            auth()->user()->role !== 'admin' &&
            $product->store_id !== auth()->user()->store_id
        ) {
            abort(403);
        }

        try {
            $validated = $request->validate([
                'name' => 'required|max:255',
                'code' => 'required|max:255|unique:products,code,' . $product->id,
                'barcode' => 'nullable|max:100|unique:products,barcode,' . $product->id,
                'description' => 'nullable|string',
                'short_description' => 'nullable|string|max:500',
                'category_id' => 'required|exists:categories,id',
                'tax_id' => 'nullable|exists:taxes,id',
                'discount_id' => 'nullable|exists:discounts,id',
                'store_id' => auth()->user()->role === 'admin' ? 'required|exists:stores,id' : '',
                'url' => 'nullable|url',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            DB::beginTransaction();

            // Handle barcode generation if changed
            $barcodePath = $product->barcode_image;
            if (isset($validated['barcode']) && $validated['barcode'] !== $product->barcode) {
                // Delete old barcode image if exists
                if ($product->barcode_image) {
                    Storage::disk('public')->delete($product->barcode_image);
                    $barcodePath = null;
                }

                // Generate new barcode if provided
                if (!empty($validated['barcode'])) {
                    $barcode = new DNS1D();
                    $barcode->setStorPath(storage_path('app/public/barcodes'));
                    $barcodeImage = $barcode->getBarcodePNG($validated['barcode'], 'C128');
                    $barcodePath = 'barcodes/' . $validated['barcode'] . '.png';
                    Storage::disk('public')->put($barcodePath, base64_decode($barcodeImage));
                }
            }

            // Get store and category data for SEO
            $store = Store::find($validated['store_id'] ?? $product->store_id);
            $category = Category::find($validated['category_id']);

            // Generate SEO fields
            $seoData = $this->generateSeoData(
                $validated['name'],
                $validated['short_description'] ?? null,
                $validated['description'] ?? null,
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
                'barcode_image' => $barcodePath,
                'category_id' => $validated['category_id'],
                'tax_id' => $validated['tax_id'] ?? null,
                'discount_id' => $validated['discount_id'] ?? null,
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
                'og_type' => 'product',

                // Schema.org fields
                'schema_brand' => $store->name,
                'schema_sku' => $validated['code'],
                'schema_gtin' => $validated['barcode'],
                'schema_mpn' => $validated['code'],

                'url' => $validated['url'] ?? null
            ]);

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

            return redirect()->route('products.show', $product)
                ->with('success', 'Produk berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            \Log::error('Product update failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return back()
                ->with('error', 'Gagal memperbarui produk: ' . $e->getMessage())
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
        if (
            auth()->user()->role !== 'admin' &&
            $product->store_id !== auth()->user()->store_id
        ) {
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
            'stockAdjustments'
        ));
    }

    public function destroy(Product $product)
    {
        if (
            auth()->user()->role !== 'admin' &&
            $product->store_id !== auth()->user()->store_id
        ) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            // Dapatkan semua product_unit_id yang terkait dengan produk ini
            $productUnitIds = $product->productUnits()->pluck('id')->toArray();

            // Hapus riwayat stok terkait dengan product_unit_id
            if (!empty($productUnitIds)) {
                // Hapus data di stock_histories terlebih dahulu
                \App\Models\StockHistory::whereIn('product_unit_id', $productUnitIds)->delete();

                // Hapus data di stock_adjustments (jika ada)
                \App\Models\StockAdjustment::whereIn('product_unit_id', $productUnitIds)->delete();

                // Hapus harga tier (prices) terkait product unit
                \App\Models\Price::whereIn('product_unit_id', $productUnitIds)->delete();
            }

            // Hapus item transaksi terkait dengan produk (jika ada)
            $product->transactionItems()->delete();

            // Hapus product units setelah menghapus data yang terhubung
            $product->productUnits()->delete();

            // Hapus gambar produk dari database
            $productImages = $product->images;
            $product->images()->delete();

            // Hapus barcode image jika ada
            if ($product->barcode_image) {
                Storage::disk('public')->delete($product->barcode_image);
            }

            // Hapus file gambar dari storage
            foreach ($productImages as $image) {
                Storage::disk('public')->delete($image->image_path);
            }

            // Terakhir hapus produk
            $product->delete();

            DB::commit();
            return redirect()
                ->route('products.index')
                ->with('success', 'Produk berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
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
        if (
            auth()->user()->role !== 'admin' &&
            $image->product->store_id !== auth()->user()->store_id
        ) {
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
