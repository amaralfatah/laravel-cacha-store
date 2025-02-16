<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Store;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index()
    {
        $query = Discount::latest();

        if (auth()->user()->role !== 'admin') {
            $query->where('store_id', auth()->user()->store_id);
        }

        $discounts = $query->paginate(10);
        return view('discounts.index', compact('discounts'));
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
            return back()->withErrors(['value' => 'Percentage discount cannot exceed 100%'])->withInput();
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
            ->with('success', 'Discount created successfully.');
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
            return back()->withErrors(['value' => 'Percentage discount cannot exceed 100%'])->withInput();
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
            ->with('success', 'Discount updated successfully.');
    }

    public function destroy(Discount $discount)
    {
        if (auth()->user()->role !== 'admin' &&
            $discount->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        if ($discount->products()->count() > 0 || $discount->transactions()->count() > 0) {
            return redirect()->route('discounts.index')
                ->with('error', 'Cannot delete discount with associated products or transactions.');
        }

        $discount->delete();
        return redirect()->route('discounts.index')
            ->with('success', 'Discount deleted successfully.');
    }
}
