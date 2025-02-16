<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Store;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $suppliers = Supplier::with('store');

            // Filter berdasarkan toko
            if (auth()->user()->role !== 'admin') {
                $suppliers->where('store_id', auth()->user()->store_id);
            }

            return DataTables::of($suppliers)
                ->addIndexColumn()
                ->addColumn('store_name', function($supplier) {
                    return $supplier->store ? $supplier->store->name : '-';
                })
                ->addColumn('action', function ($supplier) {
                    return view('suppliers.partials.actions', compact('supplier'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('suppliers.index');
    }

    public function create()
    {
        if (auth()->user()->role === 'admin') {
            $stores = Store::where('is_active', true)->get();
            return view('suppliers.create', compact('stores'));
        }
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'code' => 'required|string|max:255|unique:suppliers',
            'store_id' => auth()->user()->role === 'admin' ? 'required|exists:stores,id' : 'nullable'
        ]);

        $data = $request->all();
        if (auth()->user()->role !== 'admin') {
            $data['store_id'] = auth()->user()->store_id;
        }

        Supplier::create($data);

        return redirect()->route('suppliers.index')
            ->with('success', 'Pemasok berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        // Cek akses
        if (auth()->user()->role !== 'admin' &&
            $supplier->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        $stores = null;
        if (auth()->user()->role === 'admin') {
            $stores = Store::where('is_active', true)->get();
        }

        return view('suppliers.edit', compact('supplier', 'stores'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        // Cek akses
        if (auth()->user()->role !== 'admin' &&
            $supplier->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'code' => 'required|string|max:255|unique:suppliers,code,' . $supplier->id,
            'store_id' => auth()->user()->role === 'admin' ? 'required|exists:stores,id' : 'nullable'
        ]);

        $data = $request->all();
        if (auth()->user()->role !== 'admin') {
            $data['store_id'] = $supplier->store_id; // Pastikan store_id tidak berubah
        }

        $supplier->update($data);

        return redirect()->route('suppliers.index')
            ->with('success', 'Pemasok berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        // Cek akses
        if (auth()->user()->role !== 'admin' &&
            $supplier->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        // Cek relasi dengan produk
        if ($supplier->products()->count() > 0) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Tidak dapat menghapus pemasok yang memiliki produk.');
        }

        $supplier->delete();
        return redirect()->route('suppliers.index')
            ->with('success', 'Pemasok berhasil dihapus.');
    }
}
