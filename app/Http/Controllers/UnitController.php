<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Store;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $query = Unit::latest();

        // Filter by store for non-admin users
        if (auth()->user()->role !== 'admin') {
            $query->where('store_id', auth()->user()->store_id);
        }

        $units = $query->paginate(10);
        return view('units.index', compact('units'));
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
        ]);

        return redirect()->route('units.index')
            ->with('success', 'Unit created successfully.');
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
        ];

        if (auth()->user()->role === 'admin') {
            $validationRules['store_id'] = 'required|exists:stores,id';
        }

        $request->validate($validationRules);

        $updateData = [
            'name' => $request->name,
            'code' => $request->code,
        ];

        if (auth()->user()->role === 'admin') {
            $updateData['store_id'] = $request->store_id;
        }

        $unit->update($updateData);

        return redirect()->route('units.index')
            ->with('success', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit)
    {
        // Check if user has access to this unit
        if (auth()->user()->role !== 'admin' && $unit->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        if ($unit->productUnits()->count() > 0) {
            return redirect()->route('units.index')
                ->with('error', 'Cannot delete unit with associated products.');
        }

        $unit->delete();
        return redirect()->route('units.index')
            ->with('success', 'Unit deleted successfully.');
    }
}
