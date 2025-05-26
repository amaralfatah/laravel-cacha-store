<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use App\Models\Store;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TaxController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $taxes = Tax::with('store'); // Eager load store relationship

            if (auth()->user()->role !== 'admin') {
                $taxes->where('store_id', auth()->user()->store_id);
            }

            return DataTables::of($taxes)
                ->addIndexColumn()
                ->addColumn('store_name', function ($tax) {
                    return $tax->store ? $tax->store->name : '-';
                })
                ->addColumn('rate_formatted', function ($tax) {
                    return $tax->rate . '%';
                })
                ->addColumn('status', function ($tax) {
                    return view('taxes.partials.status', compact('tax'))->render();
                })
                ->addColumn('actions', function ($tax) {
                    return view('taxes.partials.actions', compact('tax'))->render();
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        return view('taxes.index');
    }

    public function create()
    {
        $stores = auth()->user()->role === 'admin' ? Store::all() : [];
        return view('taxes.create', compact('stores'));
    }

    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
        ];

        if (auth()->user()->role === 'admin') {
            $validationRules['store_id'] = 'required|exists:stores,id';
        }

        $request->validate($validationRules);

        Tax::create([
            'name' => $request->name,
            'rate' => $request->rate,
            'is_active' => $request->has('is_active'),
            'store_id' => auth()->user()->role === 'admin'
                ? $request->store_id
                : auth()->user()->store_id
        ]);

        return redirect()->route('taxes.index')
            ->with('success', 'Pajak berhasil ditambahkan.');
    }

    public function edit(Tax $tax)
    {
        if (
            auth()->user()->role !== 'admin' &&
            $tax->store_id !== auth()->user()->store_id
        ) {
            abort(403);
        }

        $stores = auth()->user()->role === 'admin' ? Store::all() : [];
        return view('taxes.edit', compact('tax', 'stores'));
    }

    public function update(Request $request, Tax $tax)
    {
        if (
            auth()->user()->role !== 'admin' &&
            $tax->store_id !== auth()->user()->store_id
        ) {
            abort(403);
        }

        $validationRules = [
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
        ];

        if (auth()->user()->role === 'admin') {
            $validationRules['store_id'] = 'required|exists:stores,id';
        }

        $request->validate($validationRules);

        $updateData = [
            'name' => $request->name,
            'rate' => $request->rate,
            'is_active' => $request->has('is_active')
        ];

        if (auth()->user()->role === 'admin') {
            $updateData['store_id'] = $request->store_id;
        }

        $tax->update($updateData);

        return redirect()->route('taxes.index')
            ->with('success', 'Pajak berhasil diperbarui.');
    }

    public function destroy(Tax $tax)
    {
        if (
            auth()->user()->role !== 'admin' &&
            $tax->store_id !== auth()->user()->store_id
        ) {
            abort(403);
        }

        if ($tax->products()->count() > 0 || $tax->transactions()->count() > 0) {
            return redirect()->route('taxes.index')
                ->with('error', 'Tidak dapat menghapus pajak yang memiliki produk atau transaksi terkait.');
        }

        $tax->delete();
        return redirect()->route('taxes.index')
            ->with('success', 'Pajak berhasil dihapus.');
    }
}
