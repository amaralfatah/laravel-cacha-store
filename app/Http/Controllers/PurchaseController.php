<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function searchProducts(Request $request)
    {
        $query = Product::query()
            ->with(['productUnits.unit'])
            ->select([
                'products.id',
                'products.name',
                'products.barcode',
                'products.store_id'
            ]);

        // Filter berdasarkan store untuk non-admin
        if (auth()->user()->role !== 'admin') {
            $query->where('products.store_id', auth()->user()->store_id);
        }

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('products.name', 'like', "%{$search}%")
                    ->orWhere('products.barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(10);

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $products->total(),
            'recordsFiltered' => $products->total(),
            'data' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'barcode' => $product->barcode,
                    'stock' => $product->productUnits->sum('stock'),
                    'units' => $product->productUnits->map(function ($unit) {
                        return [
                            'id' => $unit->id,
                            'name' => $unit->unit->name,
                            'purchase_price' => $unit->purchase_price,
                            'stock' => $unit->stock
                        ];
                    })
                ];
            })
        ]);
    }
}
