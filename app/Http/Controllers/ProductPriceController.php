<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PriceTier;
use App\Models\Tax;
use App\Models\Discount;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProductPriceController extends Controller
{
    public function index()
    {
        $products = Product::with(['priceTiers', 'tax', 'discount', 'defaultUnit'])->get();
        return view('product-price.index', compact('products'));
    }

    public function edit(Product $product)
    {
        $taxes = Tax::where('is_active', true)->get();
        $discounts = Discount::where('is_active', true)->get();
        $units = Unit::all();
        $priceTiers = $product->priceTiers;

        return view('product-price.edit', compact('product', 'taxes', 'discounts', 'units', 'priceTiers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'tax_id' => 'nullable|exists:taxes,id',
            'discount_id' => 'nullable|exists:discounts,id',
            'base_price' => 'required|numeric|min:0'
        ]);

        $product->update($validated);

        return redirect()->route('product-price.index')
            ->with('success', 'Harga produk berhasil diupdate');
    }

    public function storePriceTier(Request $request, Product $product)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'min_quantity' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:0'
        ]);

        $validated['product_id'] = $product->id;

        PriceTier::create($validated);

        return redirect()->back()->with('success', 'Harga bertingkat berhasil ditambahkan');
    }

    public function destroyPriceTier(PriceTier $priceTier)
    {
        $priceTier->delete();
        return redirect()->back()->with('success', 'Harga bertingkat berhasil dihapus');
    }
}
