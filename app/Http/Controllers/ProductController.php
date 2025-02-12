<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Tax;
use App\Models\Unit;
use Illuminate\Http\Request;
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
            'is_active' => 'boolean'
        ]);

        $barcode = new DNS1D();
        $barcode->setStorPath(storage_path('app/public/barcodes'));
        $barcodeImage = $barcode->getBarcodePNG($validated['barcode'], 'C128');
        $barcodePath = 'barcodes/' . $validated['barcode'] . '.png';
        Storage::disk('public')->put($barcodePath, base64_decode($barcodeImage));

        $validated['barcode_image'] = $barcodePath;

        $validated['is_active'] = $request->has('is_active');

        $product = Product::create($validated);

        $product->productUnits()->create([
            'unit_id' => $validated['default_unit_id'],
            'conversion_factor' => 1,
            'purchase_price' => $validated['purchase_price'],
            'selling_price' => $validated['selling_price'],
            'is_default' => true
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $taxes = Tax::where('is_active', true)->get();
        $discounts = Discount::where('is_active', true)->get();

        return view('products.edit', compact('product', 'categories', 'taxes', 'discounts'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'barcode' => 'required|max:100|unique:products,barcode,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'tax_id' => 'nullable|exists:taxes,id',
            'discount_id' => 'nullable|exists:discounts,id',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'productUnits.unit']);
        return view('products.show', compact('product'));
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
