<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Unit;
use App\Models\ProductUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
//        $productUnits = $product->productUnits()->with('unit')
//            ->orderBy('is_default', 'desc')
//            ->get() ?? collect(); // Pastikan selalu ada collection meski kosong
//
//        return view('product_units.index', compact('product', 'productUnits'));
//    }

    public function create(Product $product)
    {
        // Ambil unit yang aktif dan belum digunakan oleh produk
        $availableUnits = Unit::where('is_active', true)
            ->whereNotIn('id', $product->productUnits->pluck('unit_id'))
            ->get();

        // Cek apakah masih ada unit yang tersedia
        if ($availableUnits->isEmpty()) {
            return back()->with('error', 'Tidak ada unit yang tersedia untuk ditambahkan.');
        }

        return view('product_units.create', compact('product', 'availableUnits'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'unit_id' => [
                'required',
                'exists:units,id',
                function ($attribute, $value, $fail) use ($product) {
                    if ($product->productUnits()->where('unit_id', $value)->exists()) {
                        $fail('Unit ini sudah digunakan untuk produk ini.');
                    }
                },
            ],
            'conversion_factor' => 'required|numeric|min:0.0001',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'is_default' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $isFirstUnit = $product->productUnits()->count() === 0;
            $shouldBeDefault = $isFirstUnit || ($request->has('is_default') && $request->is_default);

            if ($shouldBeDefault) {
                $product->productUnits()->update(['is_default' => false]);
                $validated['is_default'] = true;
                $validated['conversion_factor'] = 1;

                // Update default_unit_id di tabel products
                $product->update(['default_unit_id' => $validated['unit_id']]);
            } else {
                $validated['is_default'] = false;

                $defaultUnit = $product->productUnits()->where('is_default', true)->first();
                if ($defaultUnit) {
                    $validated['purchase_price'] = $defaultUnit->purchase_price * $validated['conversion_factor'];
                    $validated['selling_price'] = $defaultUnit->selling_price * $validated['conversion_factor'];
                    // Konversi stock sesuai conversion factor
                    $validated['stock'] = floor($validated['stock'] / $validated['conversion_factor']);
                }
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
        return view('product_units.edit', compact('product', 'unit'));
    }

    public function update(Request $request, Product $product, ProductUnit $unit)
    {
        $validated = $request->validate([
            'conversion_factor' => 'required|numeric|min:0.0001',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'is_default' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            if ($request->has('is_default') && $request->is_default) {
                $product->productUnits()
                    ->where('id', '!=', $unit->id)
                    ->update(['is_default' => false]);

                $validated['conversion_factor'] = 1;

                // Update default_unit_id di tabel products
                $product->update(['default_unit_id' => $unit->unit_id]);

                $otherUnits = $product->productUnits()
                    ->where('id', '!=', $unit->id)
                    ->get();

                foreach ($otherUnits as $otherUnit) {
                    $newPurchasePrice = $validated['purchase_price'] * $otherUnit->conversion_factor;
                    $newSellingPrice = $validated['selling_price'] * $otherUnit->conversion_factor;
                    $newStock = floor($validated['stock'] / $otherUnit->conversion_factor);

                    $otherUnit->update([
                        'purchase_price' => $newPurchasePrice,
                        'selling_price' => $newSellingPrice,
                        'stock' => $newStock
                    ]);
                }
            } else {
                $defaultUnit = $product->productUnits()
                    ->where('is_default', true)
                    ->where('id', '!=', $unit->id)
                    ->first();

                if ($defaultUnit) {
                    $validated['purchase_price'] = $defaultUnit->purchase_price * $validated['conversion_factor'];
                    $validated['selling_price'] = $defaultUnit->selling_price * $validated['conversion_factor'];
                    $validated['stock'] = floor($validated['stock'] / $validated['conversion_factor']);
                }
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
        if ($unit->is_default && $product->productUnits()->count() > 1) {
            return back()->with('error',
                'Tidak dapat menghapus unit default selama masih ada unit lain. ' .
                'Silakan set unit lain sebagai default terlebih dahulu.'
            );
        }

        try {
            DB::beginTransaction();

            $unit->delete();

            if ($product->productUnits()->count() === 0) {
                $product->update(['default_unit_id' => null]);
            }

            DB::commit();
            return redirect()
                ->route('products.show', $product)
                ->with('success', 'Unit produk berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus unit: ' . $e->getMessage());
        }
    }
}
