<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::with(['product', 'unit'])->get();
        return view('inventory.index', compact('inventories'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        $units = Unit::all();
        return view('inventory.create', compact('products', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'unit_id' => [
                'required',
                'exists:units,id',
                Rule::unique('inventories')
                    ->where('product_id', $request->product_id)
                    ->where('unit_id', $request->unit_id)
            ],
            'quantity' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0'
        ]);

        Inventory::create($validated);
        return redirect()->route('inventory.index')->with('success', 'Stok berhasil ditambahkan');
    }

    public function edit(Inventory $inventory)
    {
        $products = Product::where('is_active', true)->get();
        $units = Unit::all();
        return view('inventory.edit', compact('inventory', 'products', 'units'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0'
        ]);

        $inventory->update($validated);
        return redirect()->route('inventory.index')->with('success', 'Stok berhasil diperbarui');
    }

    public function checkLowStock()
    {
        $lowStock = Inventory::whereRaw('quantity <= min_stock')->with(['product', 'unit'])->get();
        return response()->json($lowStock);
    }
}
