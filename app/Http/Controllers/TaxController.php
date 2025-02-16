<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use App\Models\Store;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index()
    {
        $query = Tax::latest();

        if (auth()->user()->role !== 'admin') {
            $query->where('store_id', auth()->user()->store_id);
        }

        $taxes = $query->paginate(10);
        return view('taxes.index', compact('taxes'));
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
            ->with('success', 'Tax created successfully.');
    }

    public function edit(Tax $tax)
    {
        if (auth()->user()->role !== 'admin' &&
            $tax->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        $stores = auth()->user()->role === 'admin' ? Store::all() : [];
        return view('taxes.edit', compact('tax', 'stores'));
    }

    public function update(Request $request, Tax $tax)
    {
        if (auth()->user()->role !== 'admin' &&
            $tax->store_id !== auth()->user()->store_id) {
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
            ->with('success', 'Tax updated successfully.');
    }

    public function destroy(Tax $tax)
    {
        if (auth()->user()->role !== 'admin' &&
            $tax->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        if ($tax->products()->count() > 0 || $tax->transactions()->count() > 0) {
            return redirect()->route('taxes.index')
                ->with('error', 'Cannot delete tax with associated products or transactions.');
        }

        $tax->delete();
        return redirect()->route('taxes.index')
            ->with('success', 'Tax deleted successfully.');
    }
}
