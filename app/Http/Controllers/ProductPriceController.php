<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Price;
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
            'productUnits.prices',
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
                $productUnit = ProductUnit::with('prices')
                    ->findOrFail($validated['product_unit_id']);

                // Check for conflicts with existing tiers
                $conflictingTier = $productUnit->prices()
                    ->where('min_quantity', $validated['min_quantity'])
                    ->first();

                if ($conflictingTier) {
                    throw ValidationException::withMessages([
                        'min_quantity' => 'Tingkat harga untuk jumlah ini sudah ada'
                    ]);
                }

                // Check price is lower than tiers with smaller quantities
                $lowerTier = $productUnit->prices()
                    ->where('min_quantity', '<', $validated['min_quantity'])
                    ->orderBy('min_quantity', 'desc')
                    ->first();

                if ($lowerTier && $validated['price'] >= $lowerTier->price) {
                    throw ValidationException::withMessages([
                        'price' => 'Harga harus lebih rendah dari tingkat dengan jumlah yang lebih kecil'
                    ]);
                }

                Price::create($validated);
            });

            return redirect()
                ->route('products.show', $product)
                ->with('success', 'Tingkat harga berhasil ditambahkan');

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        }
    }


    public function edit(Product $product, Price $price)
    {
        // Verify price tier belongs to product
        if ($price->productUnit->product_id !== $product->id) {
            abort(404);
        }

        // Load necessary relationships with specific ordering
        $product->load([
            'productUnits.unit',
            'productUnits.prices' => function ($query) {
                $query->orderBy('min_quantity', 'asc');
            },
            'tax',
            'discount'
        ]);

        // Get adjacent tiers for the current unit
        $currentUnit = $price->productUnit;
        $adjacentTiers = $currentUnit->prices()
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

    public function update(Request $request, Product $product, Price $price)
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
                Rule::unique('prices')
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
                        $fail('Harga harus lebih rendah dari harga dasar unit.');
                    }
                },
            ]
        ]);

        try {
            DB::transaction(function () use ($validated, $price) {
                $productUnit = ProductUnit::with(['prices' => function ($query) use ($price) {
                    $query->where('id', '!=', $price->id);
                }])->findOrFail($validated['product_unit_id']);

                // Get adjacent tiers
                $lowerTier = $productUnit->prices
                    ->where('min_quantity', '<', $validated['min_quantity'])
                    ->sortByDesc('min_quantity')
                    ->first();

                $higherTier = $productUnit->prices
                    ->where('min_quantity', '>', $validated['min_quantity'])
                    ->sortBy('min_quantity')
                    ->first();

                // Validate price against lower tier
                if ($lowerTier && $validated['price'] >= $lowerTier->price) {
                    throw ValidationException::withMessages([
                        'price' => "Harga harus lebih rendah dari {$lowerTier->price} (tingkat untuk jumlah {$lowerTier->min_quantity})"
                    ]);
                }

                // Validate price against higher tier
                if ($higherTier && $validated['price'] <= $higherTier->price) {
                    throw ValidationException::withMessages([
                        'price' => "Harga harus lebih tinggi dari {$higherTier->price} (tingkat untuk jumlah {$higherTier->min_quantity})"
                    ]);
                }

                $price->update($validated);
            });

            return redirect()
                ->route('products.show', $product)
                ->with('success', 'Tingkat harga berhasil diperbarui');

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        }
    }

    /**
     * Remove a price tier.
     */
    public function destroy(Product $product, Price $price)
    {
        // Verify price tier belongs to product
        if ($price->productUnit->product_id !== $product->id) {
            abort(404);
        }

        $price->delete();

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Tingkat harga berhasil dihapus');
    }

    /**
     * Validate price tier conflicts and pricing rules.
     *
     * @throws ValidationException
     */
    private function validatePrice(Product $product, array $data): void
    {
        // Check for conflicting tiers
        $conflictingTier = $product->prices()
            ->where('unit_id', $data['unit_id'])
            ->where('min_quantity', $data['min_quantity'])
            ->first();

        if ($conflictingTier) {
            throw ValidationException::withMessages([
                'min_quantity' => 'Sudah ada tingkat harga untuk jumlah ini!'
            ]);
        }

        // Check price is not higher than lower quantity tiers
        $lowerTier = $product->prices()
            ->where('unit_id', $data['unit_id'])
            ->where('min_quantity', '<', $data['min_quantity'])
            ->orderBy('min_quantity', 'desc')
            ->first();

        if ($lowerTier && $data['price'] >= $lowerTier->price) {
            throw ValidationException::withMessages([
                'price' => 'Harga harus lebih rendah dari tingkat dengan jumlah yang lebih kecil!'
            ]);
        }
    }
}
