<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PriceTier;
use App\Models\Tax;
use App\Models\Discount;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductPriceController extends Controller
{
    /**
     * Display a listing of products with their price information.
     */
    public function index()
    {
        $products = Product::with([
            'priceTiers',
            'tax',
            'discount',
            'defaultUnit'
        ])->get();

        return view('product-price.index', compact('products'));
    }

    /**
     * Show the form for editing product price.
     */
    public function edit(Product $product)
    {
        $data = [
            'product' => $product,
            'taxes' => Tax::where('is_active', true)->get(),
            'discounts' => Discount::where('is_active', true)->get(),
            'units' => Unit::all(),
            'priceTiers' => $product->priceTiers
        ];

        return view('product-price.edit', $data);
    }

    /**
     * Update product price information.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'tax_id' => 'nullable|exists:taxes,id',
            'discount_id' => 'nullable|exists:discounts,id',
            'base_price' => 'required|numeric|min:0'
        ]);

        try {
            $this->validatePriceTier($product, $validated);

            $product->update($validated);

            return redirect()
                ->route('product-price.index')
                ->with('success', 'Harga produk berhasil diupdate');

        } catch (ValidationException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Store a new price tier for the product.
     */
    public function storePriceTier(Request $request, Product $product)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'min_quantity' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:0'
        ]);

        try {
            DB::transaction(function () use ($product, $validated) {
                $this->validatePriceTier($product, $validated);

                PriceTier::create([
                    'product_id' => $product->id,
                    ...$validated
                ]);
            });

            return redirect()
                ->back()
                ->with('success', 'Harga bertingkat berhasil ditambahkan');

        } catch (ValidationException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove a price tier.
     */
    public function destroyPriceTier(PriceTier $priceTier)
    {
        $priceTier->delete();

        return redirect()
            ->back()
            ->with('success', 'Harga bertingkat berhasil dihapus');
    }

    /**
     * Validate price tier conflicts and pricing rules.
     *
     * @throws ValidationException
     */
    private function validatePriceTier(Product $product, array $data): void
    {
        // Check for conflicting tiers
        $conflictingTier = $product->priceTiers()
            ->where('unit_id', $data['unit_id'])
            ->where('min_quantity', $data['min_quantity'])
            ->first();

        if ($conflictingTier) {
            throw ValidationException::withMessages([
                'min_quantity' => 'Sudah ada harga untuk quantity ini!'
            ]);
        }

        // Check price is not higher than lower quantity tiers
        $lowerTier = $product->priceTiers()
            ->where('unit_id', $data['unit_id'])
            ->where('min_quantity', '<', $data['min_quantity'])
            ->orderBy('min_quantity', 'desc')
            ->first();

        if ($lowerTier && $data['price'] >= $lowerTier->price) {
            throw ValidationException::withMessages([
                'price' => 'Harga harus lebih murah dari tier quantity lebih kecil!'
            ]);
        }
    }
}
