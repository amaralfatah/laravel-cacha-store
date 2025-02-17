<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Group;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // CategoryController.php

    public function index()
    {
        $categories = Category::with(['group', 'store'])->latest();

        if (auth()->user()->role !== 'admin') {
            $categories->where('store_id', auth()->user()->store_id);
        }

        $categories = $categories->paginate(10);
        return view('categories.index', compact('categories'));
    }

    public function create()
    {

        $groups = Group::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function($query) {
                return $query->where('store_id', auth()->user()->store_id);
            })
            ->get();

        $stores = auth()->user()->role === 'admin'
            ? \App\Models\Store::all()
            : \App\Models\Store::where('id', auth()->user()->store_id)->get();

        return view('categories.create', compact('groups', 'stores'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'code' => 'required|string|max:255|unique:categories',
            'name' => 'required|string|max:255',
            'group_id' => 'required|exists:groups,id',
            'is_active' => 'boolean'
        ]);

        Category::create([
            'code' => $request->code,
            'name' => $request->name,
            'group_id' => $request->group_id,
            'is_active' => $request->has('is_active'),
            'store_id' => auth()->user()->role === 'admin'
                ? $request->store_id
                : auth()->user()->store_id
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {

        if (auth()->user()->role !== 'admin' &&
            $category->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        $groups = Group::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function($query) {
                return $query->where('store_id', auth()->user()->store_id);
            })
            ->get();

        $stores = auth()->user()->role === 'admin'
            ? \App\Models\Store::all()
            : \App\Models\Store::where('id', auth()->user()->store_id)->get();

        return view('categories.edit', compact('category', 'groups', 'stores'));
    }

    public function update(Request $request, Category $category)
    {

        if (auth()->user()->role !== 'admin' &&
            $category->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        $request->validate([
            'code' => 'required|string|max:255|unique:categories,code,' . $category->id,
            'name' => 'required|string|max:255',
            'group_id' => 'required|exists:groups,id',
            'is_active' => 'boolean'
        ]);

        $category->update([
            'code' => $request->code,
            'name' => $request->name,
            'group_id' => $request->group_id,
            'is_active' => $request->has('is_active'),
            'store_id' => $category->store_id // Keep original store_id
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {

        if (auth()->user()->role !== 'admin' &&
            $category->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Cannot delete category with associated products.');
        }

        $category->delete();
        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
