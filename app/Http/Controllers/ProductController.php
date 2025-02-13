<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Tax;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\DNS1D;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'productUnits' => function($query) {
            $query->where('is_default', true);
        }])->paginate(10);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $units = Unit::where('is_active', true)->get();
        return view('products.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'barcode' => 'required|unique:products|max:100',
            'category_id' => 'required|exists:categories,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'default_unit_id' => 'required|exists:units,id',
            'stock' => 'required|numeric|min:0',
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
                'barcode' => $validated['barcode'],
                'barcode_image' => $barcodePath,
                'category_id' => $validated['category_id'],
                'default_unit_id' => $validated['default_unit_id'],
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
                ->with('success', 'Product created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Failed to create product: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $taxes = Tax::where('is_active', true)->get();
        $discounts = Discount::where('is_active', true)->get();

        $defaultUnit = $product->productUnits()->where('is_default', true)->first();

        return view('products.edit', compact('product', 'categories', 'taxes', 'discounts', 'defaultUnit'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
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
                ->with('success', 'Product updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Failed to update product: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Product $product)
    {
        // Load relasi yang dibutuhkan dengan eager loading
        $product->load([
            'category',
            'tax',
            'discount',
            'productUnits' => function ($query) {
                $query->orderBy('is_default', 'desc')
                    ->orderBy('created_at', 'asc');
            },
            'productUnits.unit',
            'productUnits.priceTiers' => function ($query) {
                $query->orderBy('min_quantity', 'asc');
            }
        ]);

        // Ambil data untuk form
        $taxes = Tax::where('is_active', true)->get();
        $discounts = Discount::where('is_active', true)->get();

        // Filter unit yang aktif dan belum digunakan oleh produk
        $availableUnits = Unit::where('is_active', true)
            ->whereNotIn('id', $product->productUnits->pluck('unit_id'))
            ->get();

        return view('products.show', compact(
            'product',
            'availableUnits',
            'taxes',
            'discounts'
        ));
    }


    public function destroy(Product $product)
    {
        $product->productUnits()->delete();

        if ($product->barcode_image) {
            Storage::disk('public')->delete($product->barcode_image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully');
    }
}
