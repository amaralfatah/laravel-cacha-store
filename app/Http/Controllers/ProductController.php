<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\DNS1D;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'barcode' => 'required|unique:products|max:100',
            'category_id' => 'required|exists:categories,id',
            'base_price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        // Generate barcode image
        $barcode = new DNS1D();
        $barcode->setStorPath(storage_path('app/public/barcodes'));
        $barcodeImage = $barcode->getBarcodePNG($validated['barcode'], 'C128');
        $barcodePath = 'barcodes/' . $validated['barcode'] . '.png';
        Storage::disk('public')->put($barcodePath, base64_decode($barcodeImage));

        $validated['barcode_image'] = $barcodePath;

        $validated['is_active'] = $request->has('is_active');

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully');
    }

    // app/Http/Controllers/ProductController.php

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
            'base_price' => 'required|numeric|min:0',
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

    public function updatePrice(Request $request, Product $product)
    {
        $validated = $request->validate([
            'base_price' => 'required|numeric|min:0',
            'adjust_unit_prices' => 'boolean'
        ]);

        // Calculate price change percentage
        $priceChangePercent = 0;
        if ($request->adjust_unit_prices && $product->base_price > 0) {
            $priceChangePercent = ($validated['base_price'] - $product->base_price) / $product->base_price;
        }

        // Update base price
        $product->update([
            'base_price' => $validated['base_price']
        ]);

        // Adjust unit prices if requested
        if ($request->adjust_unit_prices) {
            foreach ($product->productUnits as $unit) {
                $newPrice = $unit->price * (1 + $priceChangePercent);
                $unit->update(['price' => round($newPrice, 2)]);
            }
        }

        return redirect()->route('products.show', $product)
            ->with('success', 'Product price updated successfully');
    }

    public function destroy(Product $product)
    {
        // Hapus semua product units terkait terlebih dahulu
        $product->productUnits()->delete();

        // Hapus gambar barcode dari storage jika ada
        if ($product->barcode_image) {
            Storage::disk('public')->delete($product->barcode_image);
        }

        // Baru kemudian hapus produk
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully');
    }
}
