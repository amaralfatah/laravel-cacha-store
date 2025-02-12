<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Unit;
use App\Models\ProductUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductUnitController extends Controller
{
//    public function index(Product $product)
//    {
//        // Urutkan data dengan unit default di atas
//        $productUnits = $product->productUnits()
//            ->with('unit')
//            ->orderBy('is_default', 'desc')
//            ->get();
//
//        return view('product_units.index', compact('product', 'productUnits'));
//    }

//    public function index(Product $product)
//    {
//        $productUnits = $product->productUnits()
//            ->with('unit')
//            ->orderBy('is_default', 'desc')
//            ->orderBy('created_at', 'asc')
//            ->get();
//
//        return view('product_units.index', compact('product', 'productUnits'));
//    }

    public function create(Product $product)
    {
        // Ambil unit yang aktif dan belum digunakan oleh produk
        $availableUnits = Unit::where('is_active', true)
            ->whereNotIn('id', function($query) use ($product) {
                $query->select('unit_id')
                    ->from('product_units')
                    ->where('product_id', $product->id);
            })
            ->get();

        if ($availableUnits->isEmpty()) {
            return back()->with('error', 'Tidak ada unit yang tersedia untuk ditambahkan.');
        }

        // Cek apakah sudah ada unit default
        $hasDefaultUnit = $product->productUnits()->where('is_default', true)->exists();

        return view('product_units.create', compact('product', 'availableUnits', 'hasDefaultUnit'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'unit_id' => [
                'required',
                'exists:units,id',
                Rule::unique('product_units')->where(function ($query) use ($product) {
                    return $query->where('product_id', $product->id);
                }),
            ],
            'conversion_factor' => 'required|numeric|min:0.0001|max:999999.9999',
            'purchase_price' => 'required|numeric|min:0|max:999999999.99',
            'selling_price' => 'required|numeric|min:0|max:999999999.99',
            'stock' => 'required|numeric|min:0|max:999999999.99',
            'is_default' => 'sometimes|boolean',
        ]);

        try {
            DB::beginTransaction();

            $isFirstUnit = $product->productUnits()->count() === 0;
            $shouldBeDefault = $isFirstUnit || ($request->boolean('is_default'));

            // Jika ini unit pertama atau diminta sebagai default
            if ($shouldBeDefault) {
                // Set semua unit lain menjadi non-default
                $product->productUnits()->update(['is_default' => false]);

                $validated['is_default'] = true;
                $validated['conversion_factor'] = 1.0000; // Unit default selalu 1

                $product->update(['default_unit_id' => $validated['unit_id']]);
            } else {
                $validated['is_default'] = false;

                // Ambil unit default untuk konversi
                $defaultUnit = $product->productUnits()->where('is_default', true)->first();
                if (!$defaultUnit) {
                    throw new \Exception('Tidak ada unit default yang ditemukan.');
                }

                // Konversi stok ke unit terkecil (unit default)
                $validated['stock'] = $this->convertToDefaultUnit(
                    $validated['stock'],
                    $validated['conversion_factor']
                );
            }

            $product->productUnits()->create($validated);

            DB::commit();
            return redirect()
                ->route('products.show', $product)
                ->with('success', 'Unit produk berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Gagal menambahkan unit: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Product $product, ProductUnit $unit)
    {
        // Hitung total stok dalam unit default
        $totalStockInDefaultUnit = $this->calculateTotalStockInDefaultUnit($product);

        return view('product_units.edit', compact('product', 'unit', 'totalStockInDefaultUnit'));
    }

    public function update(Request $request, Product $product, ProductUnit $unit)
    {
        $validated = $request->validate([
            'conversion_factor' => [
                'required',
                'numeric',
                'min:0.0001',
                'max:999999.9999',
                function ($attribute, $value, $fail) use ($unit) {
                    if ($unit->is_default && $value !== 1.0000) {
                        $fail('Conversion factor untuk unit default harus 1.');
                    }
                },
            ],
            'purchase_price' => 'required|numeric|min:0|max:999999999.99',
            'selling_price' => 'required|numeric|min:0|max:999999999.99',
            'stock' => 'required|numeric|min:0|max:999999999.99',
            'is_default' => 'sometimes|boolean',
        ]);

        try {
            DB::beginTransaction();

            $oldConversionFactor = $unit->conversion_factor;
            $newConversionFactor = $validated['conversion_factor'];

            if ($request->boolean('is_default')) {
                // Jika mengubah menjadi unit default
                $this->handleMakeDefault($product, $unit, $validated);
            } else if ($unit->is_default && !$request->boolean('is_default')) {
                // Mencegah unit default diubah menjadi non-default jika hanya ada satu unit
                if ($product->productUnits()->count() === 1) {
                    throw new \Exception('Tidak dapat mengubah satu-satunya unit menjadi non-default.');
                }
            }

            // Konversi stok jika conversion factor berubah
            if ($oldConversionFactor !== $newConversionFactor) {
                $validated['stock'] = $this->convertStock(
                    $validated['stock'],
                    $oldConversionFactor,
                    $newConversionFactor
                );
            }

            $unit->update($validated);

            DB::commit();
            return redirect()
                ->route('products.show', $product)
                ->with('success', 'Unit produk berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Gagal memperbarui unit: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Product $product, ProductUnit $unit)
    {
        try {
            DB::beginTransaction();

            // Cek apakah ini unit default
            if ($unit->is_default) {
                // Jika ini satu-satunya unit, boleh dihapus
                if ($product->productUnits()->count() === 1) {
                    $product->update(['default_unit_id' => null]);
                } else {
                    throw new \Exception(
                        'Tidak dapat menghapus unit default selama masih ada unit lain. ' .
                        'Silakan set unit lain sebagai default terlebih dahulu.'
                    );
                }
            }

            $unit->delete();

            DB::commit();
            return redirect()
                ->route('products.show', $product)
                ->with('success', 'Unit produk berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus unit: ' . $e->getMessage());
        }
    }

    /**
     * Convert quantity from one unit to another
     */
    protected function convertStock($quantity, $fromFactor, $toFactor)
    {
        if ($fromFactor === $toFactor) {
            return $quantity;
        }

        // Konversi ke unit default terlebih dahulu
        $inDefaultUnit = $quantity * $fromFactor;

        // Kemudian konversi ke unit tujuan
        return $inDefaultUnit / $toFactor;
    }

    /**
     * Convert quantity to default unit
     */
    protected function convertToDefaultUnit($quantity, $conversionFactor)
    {
        return $quantity * $conversionFactor;
    }

    /**
     * Calculate total stock in default unit
     */
    protected function calculateTotalStockInDefaultUnit(Product $product)
    {
        return $product->productUnits()
            ->get()
            ->sum(function ($unit) {
                return $unit->stock * $unit->conversion_factor;
            });
    }

    /**
     * Handle making a unit the default unit
     */
    protected function handleMakeDefault(Product $product, ProductUnit $unit, array &$validated)
    {
        // Set semua unit lain menjadi non-default
        $product->productUnits()
            ->where('id', '!=', $unit->id)
            ->update(['is_default' => false]);

        $validated['is_default'] = true;
        $validated['conversion_factor'] = 1.0000;

        // Update default unit di products
        $product->update(['default_unit_id' => $unit->unit_id]);

        // Update harga dan stok unit lain berdasarkan unit default baru
        $otherUnits = $product->productUnits()
            ->where('id', '!=', $unit->id)
            ->get();

        foreach ($otherUnits as $otherUnit) {
            $otherUnit->update([
                'purchase_price' => $validated['purchase_price'] * $otherUnit->conversion_factor,
                'selling_price' => $validated['selling_price'] * $otherUnit->conversion_factor,
                'stock' => $this->convertStock(
                    $validated['stock'],
                    1, // from default unit
                    $otherUnit->conversion_factor
                )
            ]);
        }
    }
}
