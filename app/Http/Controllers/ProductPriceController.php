<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PriceTier;
use App\Models\ProductUnit;
use App\Models\Tax;
use App\Models\Discount;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductPriceController extends Controller
{

    public function create(Product $product)
    {
        // Load necessary relationships
        $product->load([
            'productUnits.unit',
            'productUnits.priceTiers',
            'tax',
            'discount'
        ]);

        return view('products.product-price.create', compact('product'));
    }


    /**
     * Store a new price tier for the product.
     */
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_unit_id' => [
                'required',
                Rule::exists('product_units', 'id')->where(function ($query) use ($product) {
                    return $query->where('product_id', $product->id);
                }),
            ],
            'min_quantity' => 'required|numeric|min:0.01',
            'price' => 'required|numeric|min:0'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Validate tier pricing
                $productUnit = ProductUnit::with('priceTiers')
                    ->findOrFail($validated['product_unit_id']);

                // Check for conflicts with existing tiers
                $conflictingTier = $productUnit->priceTiers()
                    ->where('min_quantity', $validated['min_quantity'])
                    ->first();

                if ($conflictingTier) {
                    throw ValidationException::withMessages([
                        'min_quantity' => 'Price tier for this quantity already exists'
                    ]);
                }

                // Check price is lower than tiers with smaller quantities
                $lowerTier = $productUnit->priceTiers()
                    ->where('min_quantity', '<', $validated['min_quantity'])
                    ->orderBy('min_quantity', 'desc')
                    ->first();

                if ($lowerTier && $validated['price'] >= $lowerTier->price) {
                    throw ValidationException::withMessages([
                        'price' => 'Price must be lower than tiers with smaller quantities'
                    ]);
                }

                PriceTier::create($validated);
            });

            return redirect()
                ->route('products.show', $product)
                ->with('success', 'Price tier added successfully');

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();

        }
    }


    public function edit(Product $product, PriceTier $price)
    {
        // Verify price tier belongs to product
        if ($price->productUnit->product_id !== $product->id) {
            abort(404);
        }

        // Load necessary relationships with specific ordering
        $product->load([
            'productUnits.unit',
            'productUnits.priceTiers' => function ($query) {
                $query->orderBy('min_quantity', 'asc');
            },
            'tax',
            'discount'
        ]);

        // Get adjacent tiers for the current unit
        $currentUnit = $price->productUnit;
        $adjacentTiers = $currentUnit->priceTiers()
            ->where('id', '!=', $price->id)
            ->orderBy('min_quantity', 'asc')
            ->get();

        // Find previous and next tiers for validation limits
        $previousTier = $adjacentTiers->where('min_quantity', '<', $price->min_quantity)->last();
        $nextTier = $adjacentTiers->where('min_quantity', '>', $price->min_quantity)->first();

        return view('products.product-price.edit', compact(
            'product',
            'price',
            'previousTier',
            'nextTier'
        ));
    }

    public function update(Request $request, Product $product, PriceTier $price)
    {
        // Verify price tier belongs to product
        if ($price->productUnit->product_id !== $product->id) {
            abort(404);
        }

        $validated = $request->validate([
            'product_unit_id' => [
                'required',
                Rule::exists('product_units', 'id')->where(function ($query) use ($product) {
                    return $query->where('product_id', $product->id);
                }),
            ],
            'min_quantity' => [
                'required',
                'numeric',
                'min:0.01',
                // Unique validation excluding current record
                Rule::unique('price_tiers')
                    ->where(function ($query) use ($request) {
                        return $query->where('product_unit_id', $request->product_unit_id);
                    })
                    ->ignore($price->id),
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request, $price) {
                    $productUnit = ProductUnit::findOrFail($request->product_unit_id);

                    // Price cannot be higher than the unit's base price
                    if ($value >= $productUnit->selling_price) {
                        $fail('Price must be lower than the unit\'s base price.');
                    }
                },
            ]
        ]);

        try {
            DB::transaction(function () use ($validated, $price) {
                $productUnit = ProductUnit::with(['priceTiers' => function ($query) use ($price) {
                    $query->where('id', '!=', $price->id);
                }])->findOrFail($validated['product_unit_id']);

                // Get adjacent tiers
                $lowerTier = $productUnit->priceTiers
                    ->where('min_quantity', '<', $validated['min_quantity'])
                    ->sortByDesc('min_quantity')
                    ->first();

                $higherTier = $productUnit->priceTiers
                    ->where('min_quantity', '>', $validated['min_quantity'])
                    ->sortBy('min_quantity')
                    ->first();

                // Validate price against lower tier
                if ($lowerTier && $validated['price'] >= $lowerTier->price) {
                    throw ValidationException::withMessages([
                        'price' => "Price must be lower than {$lowerTier->price} (tier for quantity {$lowerTier->min_quantity})"
                    ]);
                }

                // Validate price against higher tier
                if ($higherTier && $validated['price'] <= $higherTier->price) {
                    throw ValidationException::withMessages([
                        'price' => "Price must be higher than {$higherTier->price} (tier for quantity {$higherTier->min_quantity})"
                    ]);
                }

                $price->update($validated);
            });

            return redirect()
                ->route('products.show', $product)
                ->with('success', 'Price tier updated successfully');

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        }
    }

    /**
     * Remove a price tier.
     */
    public function destroy(Product $product, PriceTier $price)
    {
        // Verify price tier belongs to product
        if ($price->productUnit->product_id !== $product->id) {
            abort(404);
        }

        $price->delete();

        return redirect()
            ->route('products.show', $product)
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
