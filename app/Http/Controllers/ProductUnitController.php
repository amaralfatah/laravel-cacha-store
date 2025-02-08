<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Unit;
use App\Models\ProductUnit;
use Illuminate\Http\Request;

class ProductUnitController extends Controller
{
    public function index(Product $product)
    {
        $productUnits = $product->productUnits()->with('unit')->get();
        return view('product_units.index', compact('product', 'productUnits'));
    }

    public function create(Product $product)
    {
        $units = Unit::where('is_active', true)->get();
        // Get units that are not yet assigned to this product
        $availableUnits = Unit::whereNotIn('id', $product->productUnits->pluck('unit_id'))->get();
        return view('product_units.create', compact('product', 'availableUnits'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'conversion_factor' => 'required|numeric|min:0.0001',
            'price' => 'required|numeric|min:0',
            'is_default' => 'boolean'
        ]);

        // If this is set as default, remove default from other units
        if ($request->has('is_default') && $request->is_default) {
            $product->productUnits()->update(['is_default' => false]);
        }

        $product->productUnits()->create($validated);

        return redirect()->route('products.units.index', $product)
            ->with('success', 'Product unit conversion added successfully');
    }

    public function edit(Product $product, ProductUnit $unit)
    {
        // Get all units for dropdown
        $units = Unit::all();

        return view('product_units.edit', compact('product', 'unit', 'units'));
    }

    public function update(Request $request, Product $product, ProductUnit $unit)
    {
        $validated = $request->validate([
            'conversion_factor' => 'required|numeric|min:0.0001',
            'price' => 'required|numeric|min:0',
            'is_default' => 'boolean'
        ]);

        // If this is set as default, remove default from other units
        if ($request->has('is_default') && $request->is_default) {
            $product->productUnits()->where('id', '!=', $unit->id)->update(['is_default' => false]);
        }

        $unit->update($validated);

        return redirect()->route('products.units.index', $product)
            ->with('success', 'Product unit conversion updated successfully');
    }

    public function destroy(Product $product, ProductUnit $unit)
    {
        if ($unit->is_default) {
            return back()->with('error', 'Cannot delete default unit conversion');
        }

        $unit->delete();

        return redirect()->route('products.units.index', $product)
            ->with('success', 'Product unit conversion deleted successfully');
    }
}
