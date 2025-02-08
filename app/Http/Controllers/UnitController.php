<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::latest()->paginate(10);
        return view('units.index', compact('units'));
    }

    public function create()
    {
        return view('units.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:units',
        ]);

        Unit::create([
            'name' => $request->name,
            'code' => $request->code,
            'is_base_unit' => $request->has('is_base_unit')
        ]);

        return redirect()->route('units.index')
            ->with('success', 'Unit created successfully.');
    }

    public function edit(Unit $unit)
    {
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:units,code,' . $unit->id,
        ]);

        $unit->update([
            'name' => $request->name,
            'code' => $request->code,
            'is_base_unit' => $request->has('is_base_unit')
        ]);

        return redirect()->route('units.index')
            ->with('success', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->productUnits()->count() > 0) {
            return redirect()->route('units.index')
                ->with('error', 'Cannot delete unit with associated products.');
        }

        $unit->delete();
        return redirect()->route('units.index')
            ->with('success', 'Unit deleted successfully.');
    }
}
