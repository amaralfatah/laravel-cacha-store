<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Tax;
use Illuminate\Http\Request;

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

        $validated['is_active'] = $request->has('is_active');

        Product::create($validated);
        return redirect()->route('products.index')->with('success', 'Product created successfully');
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

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }
}
