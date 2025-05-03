<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Store;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $discounts = Discount::with('store'); // Eager load store relationship

            if (auth()->user()->role !== 'admin') {
                $discounts->where('store_id', auth()->user()->store_id);
            }

            return DataTables::of($discounts)
                ->addIndexColumn()
                ->addColumn('store_name', function ($discount) {
                    return $discount->store ? $discount->store->name : '-';
                })
                ->addColumn('type_formatted', function ($discount) {
                    return ucfirst($discount->type);
                })
                ->addColumn('value_formatted', function ($discount) {
                    if ($discount->type === 'percentage') {
                        return number_format($discount->value, 2) . '%';
                    } else {
                        return 'Rp ' . number_format($discount->value, 0);
                    }
                })
                ->addColumn('status', function ($discount) {
                    return view('discounts.partials.status', compact('discount'))->render();
                })
                ->addColumn('actions', function ($discount) {
                    return view('discounts.partials.actions', compact('discount'))->render();
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        return view('discounts.index');
    }

    public function create()
    {
        $stores = auth()->user()->role === 'admin' ? Store::all() : [];
        return view('discounts.create', compact('stores'));
    }

    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
        ];

        if (auth()->user()->role === 'admin') {
            $validationRules['store_id'] = 'required|exists:stores,id';
        }

        $request->validate($validationRules);

        if ($request->type === 'percentage' && $request->value > 100) {
            return back()->withErrors(['value' => 'Persentase diskon tidak boleh melebihi 100%'])->withInput();
        }

        Discount::create([
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'is_active' => $request->has('is_active'),
            'store_id' => auth()->user()->role === 'admin'
                ? $request->store_id
                : auth()->user()->store_id
        ]);

        return redirect()->route('discounts.index')
            ->with('success', 'Diskon berhasil ditambahkan.');
    }

    public function edit(Discount $discount)
    {
        if (auth()->user()->role !== 'admin' &&
            $discount->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        $stores = auth()->user()->role === 'admin' ? Store::all() : [];
        return view('discounts.edit', compact('discount', 'stores'));
    }

    public function update(Request $request, Discount $discount)
    {
        if (auth()->user()->role !== 'admin' &&
            $discount->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        $validationRules = [
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
        ];

        if (auth()->user()->role === 'admin') {
            $validationRules['store_id'] = 'required|exists:stores,id';
        }

        $request->validate($validationRules);

        if ($request->type === 'percentage' && $request->value > 100) {
            return back()->withErrors(['value' => 'Persentase diskon tidak boleh melebihi 100%'])->withInput();
        }

        $updateData = [
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'is_active' => $request->has('is_active')
        ];

        if (auth()->user()->role === 'admin') {
            $updateData['store_id'] = $request->store_id;
        }

        $discount->update($updateData);

        return redirect()->route('discounts.index')
            ->with('success', 'Diskon berhasil diperbarui.');
    }

    public function destroy(Discount $discount)
    {
        if (auth()->user()->role !== 'admin' &&
            $discount->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        if ($discount->products()->count() > 0 || $discount->transactions()->count() > 0) {
            return redirect()->route('discounts.index')
                ->with('error', 'Tidak dapat menghapus diskon yang memiliki produk atau transaksi terkait.');
        }

        $discount->delete();
        return redirect()->route('discounts.index')
            ->with('success', 'Diskon berhasil dihapus.');
    }
}
