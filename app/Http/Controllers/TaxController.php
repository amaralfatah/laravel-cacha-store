<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index()
    {
        $taxes = Tax::latest()->paginate(10);
        return view('taxes.index', compact('taxes'));
    }

    public function create()
    {
        return view('taxes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean'
        ]);

        Tax::create([
            'name' => $request->name,
            'rate' => $request->rate,
            'is_active' => $request->is_active ?? true
        ]);

        return redirect()->route('taxes.index')
            ->with('success', 'Tax created successfully.');
    }

    public function edit(Tax $tax)
    {
        return view('taxes.edit', compact('tax'));
    }

    public function update(Request $request, Tax $tax)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean'
        ]);

        $tax->update([
            'name' => $request->name,
            'rate' => $request->rate,
            'is_active' => $request->is_active ?? true
        ]);

        return redirect()->route('taxes.index')
            ->with('success', 'Tax updated successfully.');
    }

    public function destroy(Tax $tax)
    {
        if ($tax->products()->count() > 0 || $tax->transactions()->count() > 0) {
            return redirect()->route('taxes.index')
                ->with('error', 'Cannot delete tax with associated products or transactions.');
        }

        $tax->delete();
        return redirect()->route('taxes.index')
            ->with('success', 'Tax deleted successfully.');
    }
}
