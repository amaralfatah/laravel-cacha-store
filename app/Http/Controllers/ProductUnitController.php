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
    public function create(Product $product)
    {
        $availableUnits = Unit::where('is_active', true)
            ->whereNotIn('id', $product->productUnits()->pluck('unit_id'))
            ->get();

        if ($availableUnits->isEmpty()) {
            return back()->with('error', 'Tidak ada unit yang tersedia.');
        }

        $hasDefaultUnit = $product->productUnits()->where('is_default', true)->exists();

        return view('products.product_units.create', compact('product', 'availableUnits', 'hasDefaultUnit'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'unit_id' => [
                'required',
                'exists:units,id',
                Rule::unique('product_units')->where(
                    fn($query) =>
                    $query->where('product_id', $product->id)
                ),
            ],
            'conversion_factor' => 'required|numeric|min:0.01',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'is_default' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Check if this is first unit or marked as default
            $isFirstUnit = $product->productUnits()->count() === 0;
            $shouldBeDefault = $isFirstUnit || $request->boolean('is_default');

            if ($shouldBeDefault) {
                // Reset other units' default status
                $product->productUnits()->update(['is_default' => false]);
                $validated['is_default'] = true;
                $validated['conversion_factor'] = 1.00;
                $product->update(['default_unit_id' => $validated['unit_id']]);
            }

            // Format nilai desimal
            $validated['conversion_factor'] = number_format((float) $validated['conversion_factor'], 2, '.', '');

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
        // Mendapatkan semua unit yang tersedia (yang aktif dan tidak digunakan oleh produk ini, atau unit yang sedang diedit)
        $availableUnits = Unit::where('is_active', true)
            ->where(function ($query) use ($product, $unit) {
                $query->whereNotIn('id', $product->productUnits()->where('id', '!=', $unit->id)->pluck('unit_id'))
                    ->orWhere('id', $unit->unit_id);
            })
            ->get();

        return view('products.product_units.edit', compact('product', 'unit', 'availableUnits'));
    }

    public function update(Request $request, Product $product, ProductUnit $unit)
    {
        $validated = $request->validate([
            'unit_id' => [
                'required',
                'exists:units,id',
                Rule::unique('product_units')->where(
                    fn($query) =>
                    $query->where('product_id', $product->id)
                )->ignore($unit->id),
            ],
            'conversion_factor' => 'required|numeric|min:0.01',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|numeric', // Hapus min:0 agar dapat menerima nilai negatif
            'min_stock' => 'required|numeric|min:0',
            'is_default' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $willBeDefault = $request->boolean('is_default');

            // If unit will be default
            if ($willBeDefault) {
                // Reset other units' default status
                $product->productUnits()
                    ->where('id', '!=', $unit->id)
                    ->update(['is_default' => false]);

                $validated['is_default'] = true;
                $validated['conversion_factor'] = 1.00;
                $product->update(['default_unit_id' => $validated['unit_id']]);
            }

            // Format nilai desimal
            $validated['conversion_factor'] = number_format((float) $validated['conversion_factor'], 2, '.', '');

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
            if ($unit->is_default && $product->productUnits()->count() > 1) {
                throw new \Exception('Tidak dapat menghapus unit default jika masih ada unit lain.');
            }

            $unit->delete();

            if ($unit->is_default) {
                $product->update(['default_unit_id' => null]);
            }

            return redirect()
                ->route('products.show', $product)
                ->with('success', 'Unit produk berhasil dihapus');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus unit: ' . $e->getMessage());
        }
    }
}
