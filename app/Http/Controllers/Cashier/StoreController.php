<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
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
            // Store directly in public/images/stores directory
            $file->move(public_path('images/stores'), $filename);
            $validated['logo'] = '/images/stores/' . $filename;
        }

        $validated['is_active'] = $request->has('is_active');

        Store::create($validated);

        return redirect()
            ->route('stores.index')
            ->with('success', 'Store created successfully.');
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

        return redirect()->route('stores.edit', $store)
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
