<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('store'); // Eager load store relationship

            // Jika bukan super admin, hanya tampilkan user dari toko yang sama
            if (auth()->user()->role !== 'admin') {
                $users->where('store_id', auth()->user()->store_id);
            }

            return DataTables::of($users)
                ->addIndexColumn()
                ->editColumn('role', function($user) {
                    return ucfirst($user->role);
                })
                ->addColumn('store_name', function($user) {
                    return $user->store ? $user->store->name : '-';
                })
                ->addColumn('actions', function ($user) {
                    return view('users.partials.actions', compact('user'))->render();
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('users.index');
    }

    public function create()
    {
        $roles = ['cashier']; // Role default untuk user biasa

        // Jika super admin, tambahkan opsi role admin
        if (auth()->user()->role === 'admin') {
            $roles[] = 'admin';
        }

        // Ambil data stores yang aktif
        $stores = Store::where('is_active', true)->get();

        return view('users.create', compact('roles', 'stores'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,cashier',
            'store_id' => 'required|exists:stores,id'  // Tambahkan validasi ini
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan');
    }


    public function edit(User $user)
    {
        $roles = ['cashier'];
        if (auth()->user()->role === 'admin') {
            $roles[] = 'admin';
        }

        // Ambil data stores yang aktif
        $stores = Store::where('is_active', true)->get();

        return view('users.edit', compact('user', 'roles', 'stores'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|string|in:admin,cashier',
            'store_id' => 'required|exists:stores,id'  // Tambahkan validasi ini
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        // Cek akses
        if (auth()->user()->role !== 'admin' &&
            $user->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        // Jangan hapus diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }
}
