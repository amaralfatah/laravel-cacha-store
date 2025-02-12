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

    public function index(Product $product)
    {
        $productUnits = $product->productUnits()->with('unit')
            ->orderBy('is_default', 'desc')
            ->get() ?? collect(); // Pastikan selalu ada collection meski kosong

        return view('product_units.index', compact('product', 'productUnits'));
    }

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
        // Validasi input
        $validated = $request->validate([
            'unit_id' => [
                'required',
                'exists:units,id',
                // Pastikan unit belum digunakan untuk produk ini
                function ($attribute, $value, $fail) use ($product) {
                    if ($product->productUnits()->where('unit_id', $value)->exists()) {
                        $fail('Unit ini sudah digunakan untuk produk ini.');
                    }
                },
            ],
            'conversion_factor' => 'required|numeric|min:0.0001',
            'price' => 'required|numeric|min:0',
            'is_default' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Cek apakah ini unit pertama
            $isFirstUnit = $product->productUnits()->count() === 0;

            // Tentukan apakah unit ini akan menjadi default
            $shouldBeDefault = $isFirstUnit || ($request->has('is_default') && $request->is_default);

            // Jika akan menjadi default
            if ($shouldBeDefault) {
                // Hapus status default dari unit lain
                $product->productUnits()->update(['is_default' => false]);

                // Update harga dasar produk
                $product->update(['base_price' => $validated['price']]);

                $validated['is_default'] = true;
                $validated['conversion_factor'] = 1; // Unit default selalu memiliki faktor konversi 1
            } else {
                $validated['is_default'] = false;

                // Hitung harga berdasarkan unit default
                $defaultUnit = $product->productUnits()->where('is_default', true)->first();
                if ($defaultUnit) {
                    $validated['price'] = $defaultUnit->price * $validated['conversion_factor'];
                }
            }

            // Buat product unit baru
            $product->productUnits()->create($validated);

            DB::commit();
            return redirect()
                ->route('products.units.index', $product)
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
        // Validasi input
        $validated = $request->validate([
            'conversion_factor' => 'required|numeric|min:0.0001',
            'price' => 'required|numeric|min:0',
            'is_default' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // Cek apakah unit ini akan dijadikan default
            if ($request->has('is_default') && $request->is_default) {
                // Hapus status default dari unit lain
                $product->productUnits()
                    ->where('id', '!=', $unit->id)
                    ->update(['is_default' => false]);

                // Set faktor konversi menjadi 1 untuk unit default
                $validated['conversion_factor'] = 1;

                // Update harga dasar produk
                $product->update(['base_price' => $validated['price']]);

                // Update harga unit lain berdasarkan unit default baru
                $otherUnits = $product->productUnits()
                    ->where('id', '!=', $unit->id)
                    ->get();

                foreach ($otherUnits as $otherUnit) {
                    $newPrice = $validated['price'] * $otherUnit->conversion_factor;
                    $otherUnit->update(['price' => $newPrice]);
                }
            } else {
                // Jika bukan default, hitung harga berdasarkan unit default
                $defaultUnit = $product->productUnits()
                    ->where('is_default', true)
                    ->where('id', '!=', $unit->id)
                    ->first();

                if ($defaultUnit) {
                    $validated['price'] = $defaultUnit->price * $validated['conversion_factor'];
                }
            }

            $unit->update($validated);

            DB::commit();
            return redirect()
                ->route('products.units.index', $product)
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
        // Cek apakah ini unit default dan masih ada unit lain
        if ($unit->is_default && $product->productUnits()->count() > 1) {
            return back()->with('error',
                'Tidak dapat menghapus unit default selama masih ada unit lain. ' .
                'Silakan set unit lain sebagai default terlebih dahulu.'
            );
        }

        try {
            DB::beginTransaction();

            $unit->delete();

            // Jika ini unit terakhir, reset harga dasar produk
            if ($product->productUnits()->count() === 0) {
                $product->update(['base_price' => 0]);
            }

            DB::commit();
            return redirect()
                ->route('products.units.index', $product)
                ->with('success', 'Unit produk berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus unit: ' . $e->getMessage());
        }
    }
}
