<?php

namespace App\Http\Controllers;

use App\Models\ProductUnit;
use App\Models\Store;
use App\Models\StoreBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    /**
     * Display a listing of stores.
     */
    public function index(Request $request)
    {
        $query = Store::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $stores = $query->latest()->paginate(10);

        return view('stores.index', compact('stores'));
    }

    public function show(Store $store)
    {

        // Load counts using withCount
        $store->loadCount(['products', 'customers', 'transactions']);
        $store->load('storeBalance');

        // Calculate total inventory value (price * quantity) for all products
        $totalInventoryValue = ProductUnit::join('products', 'product_units.product_id', '=', 'products.id')
            ->where('products.store_id', $store->id)
            ->where('products.is_active', true)
            ->select(DB::raw('SUM(product_units.stock * product_units.selling_price) as total_value'))
            ->first()
            ->total_value ?? 0;

        return view('stores.show', compact('store', 'totalInventoryValue'));
    }

    /**
     * Show the form for creating a new store.
     */
    public function create()
    {
        return view('stores.create');
    }

    /**
     * Store a newly created store.
     */
    /**
     * Store a newly created store.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:stores',
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean'
        ]);

        // Handle is_active checkbox
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'store-' . Str::random(20) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/stores'), $filename);
            $validated['logo'] = '/images/stores/' . $filename;
        }

        DB::beginTransaction();
        try {
            // Create store
            $store = Store::create($validated);

            // Create initial store balance
            StoreBalance::create([
                'store_id' => $store->id,
                'cash_amount' => 0,
                'non_cash_amount' => 0,
                'last_updated_by' => auth()->id()
            ]);

            DB::commit();

            return redirect()
                ->route('stores.index')
                ->with('success', 'Store created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            // If logo was uploaded, delete it
            if (isset($validated['logo'])) {
                $logoPath = public_path($validated['logo']);
                if (file_exists($logoPath)) {
                    unlink($logoPath);
                }
            }

            return redirect()
                ->back()
                ->with('error', 'Failed to create store. ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Store $store)
    {
        return view('stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'code' => 'required|unique:stores,code,' . $store->id,
            'name' => 'required|max:255',
            'address' => 'required',
            'phone' => 'required|max:20',
            'email' => 'required|email|max:255',
            'is_active' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($store->logo) {
                $oldLogoPath = public_path($store->logo);
                if (file_exists($oldLogoPath)) {
                    unlink($oldLogoPath);
                }
            }

            // Store new logo
            $file = $request->file('logo');
            $filename = 'store-' . Str::random(20) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/stores'), $filename);
            $validated['logo'] = '/images/stores/' . $filename;
        }

        $validated['is_active'] = $request->has('is_active');

        $store->update($validated);

        return redirect()->route('stores.index', $store)
            ->with('success', 'Store updated successfully');
    }

    /**
     * Remove the specified store.
     */
    public function destroy(Store $store)
    {
        // Delete logo if exists
        if ($store->logo) {
            $logoPath = public_path($store->logo);
            if (file_exists($logoPath)) {
                unlink($logoPath);
            }
        }

        $store->delete();

        return redirect()
            ->route('stores.index')
            ->with('success', 'Store deleted successfully.');
    }

    /**
     * Toggle store active status.
     */
    public function toggleStatus(Store $store)
    {
        $store->update([
            'is_active' => !$store->is_active
        ]);

        return redirect()
            ->back()
            ->with('success', 'Store status updated successfully.');
    }
}
