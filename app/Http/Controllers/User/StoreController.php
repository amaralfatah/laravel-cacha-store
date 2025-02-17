<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function show()
    {
        $store = Auth::user()->store;

        // Load counts using withCount
        $store->loadCount(['products', 'customers', 'transactions']);
        $store->load('storeBalance');

        return view('user.store.show', compact('store'));
    }
    public function edit()
    {
        $store = Auth::user()->store;
        return view('user.store.edit', compact('store'));
    }

    public function update(Request $request)
    {
        $store = Auth::user()->store;

        $validated = $request->validate([
            'name' => 'required|max:255',
            'address' => 'required',
            'phone' => 'required|max:20',
            'email' => 'required|email|max:255',
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

        $store->update($validated);

        return redirect()->route('user.store.show')
            ->with('success', 'Store updated successfully');
    }
}
