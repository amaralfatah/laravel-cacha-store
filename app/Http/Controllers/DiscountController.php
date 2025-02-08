<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::latest()->paginate(10);
        return view('discounts.index', compact('discounts'));
    }

    public function create()
    {
        return view('discounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
        ]);

        // Additional validation for percentage type
        if ($request->type === 'percentage' && $request->value > 100) {
            return back()->withErrors(['value' => 'Percentage discount cannot exceed 100%'])->withInput();
        }

        Discount::create([
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'is_active' => $request->has('is_active') // Menggunakan has() untuk mengecek keberadaan field
        ]);

        return redirect()->route('discounts.index')
            ->with('success', 'Discount created successfully.');
    }

    public function edit(Discount $discount)
    {
        return view('discounts.edit', compact('discount'));
    }

    public function update(Request $request, Discount $discount)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
        ]);

        // Additional validation for percentage type
        if ($request->type === 'percentage' && $request->value > 100) {
            return back()->withErrors(['value' => 'Percentage discount cannot exceed 100%'])->withInput();
        }

        $discount->update([
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'is_active' => $request->has('is_active') // Menggunakan has() untuk mengecek keberadaan field
        ]);

        return redirect()->route('discounts.index')
            ->with('success', 'Discount updated successfully.');
    }

    public function destroy(Discount $discount)
    {
        if ($discount->products()->count() > 0 || $discount->transactions()->count() > 0) {
            return redirect()->route('discounts.index')
                ->with('error', 'Cannot delete discount with associated products or transactions.');
        }

        $discount->delete();
        return redirect()->route('discounts.index')
            ->with('success', 'Discount deleted successfully.');
    }
}
