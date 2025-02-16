<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Product;
use App\Models\Category;
use App\Models\Discount;
use App\Models\StockAdjustment;
use App\Models\Supplier;
use App\Models\Tax;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
            'code' => 'required|unique:products|max:100',
            'barcode' => 'required|unique:products|max:100',
            'category_id' => 'required|exists:categories,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'default_unit_id' => 'required|exists:units,id',
            'stock' => 'required|numeric|min:0',
            'store_id' => auth()->user()->role === 'admin' ? 'required|exists:stores,id' : '',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $barcode = new DNS1D();
            $barcode->setStorPath(storage_path('app/public/barcodes'));
            $barcodeImage = $barcode->getBarcodePNG($validated['barcode'], 'C128');
            $barcodePath = 'barcodes/' . $validated['barcode'] . '.png';
            Storage::disk('public')->put($barcodePath, base64_decode($barcodeImage));

            $validated['barcode_image'] = $barcodePath;
            $validated['is_active'] = $request->has('is_active');

            $product = Product::create([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'barcode' => $validated['barcode'],
                'barcode_image' => $barcodePath,
                'category_id' => $validated['category_id'],
                'default_unit_id' => $validated['default_unit_id'],
                'store_id' => auth()->user()->role === 'admin'
                    ? $validated['store_id']
                    : auth()->user()->store_id,
                'is_active' => $validated['is_active']
            ]);

            // Buat product unit default
            $product->productUnits()->create([
                'unit_id' => $validated['default_unit_id'],
                'conversion_factor' => 1,
                'purchase_price' => $validated['purchase_price'],
                'selling_price' => $validated['selling_price'],
                'stock' => $validated['stock'],
                'is_default' => true
            ]);

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

    public function edit(Product $product)
    {
        // Cek akses
        if (auth()->user()->role !== 'admin' &&
            $product->store_id !== auth()->user()->store_id) {
            abort(403);
        }

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
            'code' => 'required|max:100|unique:products,code,' . $product->id,
            'barcode' => 'required|max:100|unique:products,barcode,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'tax_id' => 'nullable|exists:taxes,id',
            'discount_id' => 'nullable|exists:discounts,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $validated['is_active'] = $request->has('is_active');
            $product->update($validated);

            // Update default unit prices and stock
            $defaultUnit = $product->productUnits()->where('is_default', true)->first();
            if ($defaultUnit) {
                $defaultUnit->update([
                    'purchase_price' => $validated['purchase_price'],
                    'selling_price' => $validated['selling_price'],
                    'stock' => $validated['stock']
                ]);

                // Update other units prices and stock based on conversion factor
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

            DB::commit();
            return redirect()->route('products.index')
                ->with('success', 'Produk Berhasil Diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Gagal memperbarui produk: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Product $product)
    {
        // Load relasi yang dibutuhkan dengan eager loading
        $product->load([
            'store',
            'category.group',
            'supplier',
            'tax',
            'discount',
            'productUnits' => function ($query) {
                $query->orderBy('is_default', 'desc')
                    ->orderBy('created_at', 'asc');
            },
            'productUnits.unit',
            'productUnits.prices' => function ($query) {
                $query->orderBy('min_quantity', 'asc');
            },
            'transactionItems' => function ($query) {
                $query->with(['transaction:id,invoice_number,invoice_date', 'unit:id,code'])
                    ->latest()
                    ->take(5);
            },
            'stockHistories' => function ($query) {
                $query->with('productUnit.unit:id,code')
                    ->latest()
                    ->take(20);
            }
        ]);

        // Ambil data untuk form dan modal
        $taxes = Cache::remember('active_taxes', 60, function () {
            return Tax::where('is_active', true)
                ->select('id', 'name', 'rate')
                ->get();
        });

        $discounts = Cache::remember('active_discounts', 60, function () {
            return Discount::where('is_active', true)
                ->select('id', 'name', 'type', 'value')
                ->get();
        });

        // Filter unit yang aktif dan belum digunakan oleh produk
        $availableUnits = Cache::remember(
            "available_units_product_{$product->id}",
            60,
            function () use ($product) {
                return Unit::where('is_active', true)
                    ->whereNotIn('id', $product->productUnits->pluck('unit_id'))
                    ->select('id', 'name', 'code')
                    ->get();
            }
        );

        // Hitung statistik untuk produk
        $statistics = [
            'total_sales' => $product->transactionItems()
                ->whereHas('transaction', function ($query) {
                    $query->where('status', 'success');
                })
                ->sum('quantity'),
            'total_revenue' => $product->transactionItems()
                ->whereHas('transaction', function ($query) {
                    $query->where('status', 'success');
                })
                ->sum('subtotal'),
            'stock_value' => $product->productUnits->sum(function ($unit) {
                return $unit->stock * $unit->purchase_price;
            }),
            'potential_revenue' => $product->productUnits->sum(function ($unit) {
                return $unit->stock * $unit->selling_price;
            })
        ];

        // Ambil riwayat penyesuaian stok
        $stockAdjustments = StockAdjustment::with('user:id,name')
            ->whereIn('product_unit_id', $product->productUnits->pluck('id'))
            ->latest()
            ->take(10)
            ->get();

        return view('products.show', compact(
            'product',
            'availableUnits',
            'taxes',
            'discounts',
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

        $product->productUnits()->delete();

        if ($product->barcode_image) {
            Storage::disk('public')->delete($product->barcode_image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus');
    }
}
