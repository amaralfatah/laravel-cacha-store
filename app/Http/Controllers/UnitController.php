<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Store;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $units = Unit::with('store'); // Eager load store

            // Filter by store for non-admin users
            if (auth()->user()->role !== 'admin') {
                $units->where('store_id', auth()->user()->store_id);
            }

            return DataTables::of($units)
                ->addIndexColumn()
                ->addColumn('store_name', function ($unit) {
                    return $unit->store ? $unit->store->name : '-';
                })
                ->addColumn('status', function ($unit) {
                    return view('units.partials.status', compact('unit'))->render();
                })
                ->addColumn('actions', function ($unit) {
                    return view('units.partials.actions', compact('unit'))->render();
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        return view('units.index');
    }

    public function create()
    {
        // Get stores for admin users
        $stores = auth()->user()->role === 'admin' ? Store::all() : [];
        return view('units.create', compact('stores'));
    }

    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:units',
            'is_active' => 'boolean',
        ];

        // Add store validation for admin
        if (auth()->user()->role === 'admin') {
            $validationRules['store_id'] = 'required|exists:stores,id';
        }

        $request->validate($validationRules);

        // Set store_id based on user role
        $storeId = auth()->user()->role === 'admin'
            ? $request->store_id
            : auth()->user()->store_id;

        Unit::create([
            'name' => $request->name,
            'code' => $request->code,
            'store_id' => $storeId,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('units.index')
            ->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function edit(Unit $unit)
    {
        // Check if user has access to this unit
        if (auth()->user()->role !== 'admin' && $unit->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        $stores = auth()->user()->role === 'admin' ? Store::all() : [];
        return view('units.edit', compact('unit', 'stores'));
    }

    public function update(Request $request, Unit $unit)
    {
        // Check if user has access to this unit
        if (auth()->user()->role !== 'admin' && $unit->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        $validationRules = [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:units,code,' . $unit->id,
            'is_active' => 'boolean',
        ];

        if (auth()->user()->role === 'admin') {
            $validationRules['store_id'] = 'required|exists:stores,id';
        }

        $request->validate($validationRules);

        $updateData = [
            'name' => $request->name,
            'code' => $request->code,
            'is_active' => $request->has('is_active'),
        ];

        if (auth()->user()->role === 'admin') {
            $updateData['store_id'] = $request->store_id;
        }

        $unit->update($updateData);

        return redirect()->route('units.index')
            ->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        // Check if user has access to this unit
        if (auth()->user()->role !== 'admin' && $unit->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        if ($unit->productUnits()->count() > 0) {
            return redirect()->route('units.index')
                ->with('error', 'Tidak dapat menghapus satuan yang memiliki produk terkait.');
        }

        $unit->delete();
        return redirect()->route('units.index')
            ->with('success', 'Satuan berhasil dihapus.');
    }
}
