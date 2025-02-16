<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Store;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $customers = Customer::with('store');

            // Filter berdasarkan toko
            if (auth()->user()->role !== 'admin') {
                $customers->where('store_id', auth()->user()->store_id);
            }

            return DataTables::of($customers)
                ->addIndexColumn()
                ->addColumn('store_name', function($customer) {
                    return $customer->store ? $customer->store->name : '-';
                })
                ->addColumn('actions', function ($customer) {
                    return view('customers.partials.actions', compact('customer'))->render();
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('customers.index');
    }

    public function create()
    {
        if (auth()->user()->role === 'admin') {
            $stores = Store::where('is_active', true)->get();
            return view('customers.create', compact('stores'));
        }
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'store_id' => auth()->user()->role === 'admin' ? 'required|exists:stores,id' : 'nullable'
        ]);

        $data = $request->all();
        if (auth()->user()->role !== 'admin') {
            $data['store_id'] = auth()->user()->store_id;
        }

        Customer::create($data);

        return redirect()->route('customers.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        // Cek akses
        if (auth()->user()->role !== 'admin' &&
            $customer->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        $stores = null;
        if (auth()->user()->role === 'admin') {
            $stores = Store::where('is_active', true)->get();
        }

        return view('customers.edit', compact('customer', 'stores'));
    }

    public function update(Request $request, Customer $customer)
    {
        // Cek akses
        if (auth()->user()->role !== 'admin' &&
            $customer->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'store_id' => auth()->user()->role === 'admin' ? 'required|exists:stores,id' : 'nullable'
        ]);

        $data = $request->all();
        if (auth()->user()->role !== 'admin') {
            $data['store_id'] = $customer->store_id; // Pastikan store_id tidak berubah
        }

        $customer->update($data);

        return redirect()->route('customers.index')
            ->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        // Cek akses
        if (auth()->user()->role !== 'admin' &&
            $customer->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        if ($customer->transactions()->count() > 0) {
            return redirect()->route('customers.index')
                ->with('error', 'Tidak dapat menghapus pelanggan yang memiliki riwayat transaksi.');
        }

        $customer->delete();
        return redirect()->route('customers.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }
}
