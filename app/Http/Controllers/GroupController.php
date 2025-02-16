<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $groups = Group::with('store'); // Eager load store

            if (auth()->user()->role !== 'admin') {
                $groups->where('store_id', auth()->user()->store_id);
            }

            return DataTables::of($groups)
                ->addIndexColumn()
                ->addColumn('store_name', function ($group) {
                    return $group->store ? $group->store->name : '-';
                })
                ->addColumn('status', function ($group) {
                    return view('groups.partials.status', compact('group'))->render();
                })
                ->addColumn('actions', function ($group) {
                    return view('groups.partials.actions', compact('group'))->render();
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        return view('groups.index');
    }

    public function create()
    {
        $stores = auth()->user()->role === 'admin'
            ? \App\Models\Store::all()
            : \App\Models\Store::where('id', auth()->user()->store_id)->get();
        return view('groups.create',compact('stores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:groups,code|max:255',
            'name' => 'required|max:255',
            'is_active' => 'boolean'
        ]);

        Group::create([
            'code' => $request->code,
            'name' => $request->name,
            'is_active' => $request->is_active ?? true,
            'store_id' => auth()->user()->role === 'admin'
                ? $request->store_id
                : auth()->user()->store_id
        ]);

        return redirect()->route('groups.index')
            ->with('success', 'Kelompok berhasil ditambahkan.');
    }

    public function edit(Group $group)
    {
        // Cek akses
        if (auth()->user()->role !== 'admin' &&
            $group->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        $stores = auth()->user()->role === 'admin'
            ? \App\Models\Store::all()
            : \App\Models\Store::where('id', auth()->user()->store_id)->get();

        return view('groups.edit', compact('group', 'stores'));
    }

    public function update(Request $request, Group $group)
    {
        // Cek akses
        if (auth()->user()->role !== 'admin' &&
            $group->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        $request->validate([
            'code' => 'required|max:255|unique:groups,code,' . $group->id,
            'name' => 'required|max:255',
            'is_active' => 'boolean'
        ]);

        $group->update([
            'code' => $request->code,
            'name' => $request->name,
            'is_active' => $request->is_active ?? false,
            'store_id' => $group->store_id // Pastikan store_id tidak berubah
        ]);

        return redirect()->route('groups.index')
            ->with('success', 'Kelompok berhasil diperbarui');
    }

    public function destroy(Group $group)
    {
        // Cek akses
        if (auth()->user()->role !== 'admin' &&
            $group->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        // Cek apakah group memiliki kategori
        if ($group->categories()->count() > 0) {
            return redirect()->route('groups.index')
                ->with('error', 'Tidak dapat menghapus kelompok yang memiliki kategori.');
        }

        $group->delete();
        return redirect()->route('groups.index')
            ->with('success', 'Kelompok berhasil dihapus');
    }
}
