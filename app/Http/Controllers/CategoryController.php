<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Group;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('group')->latest()->paginate(10);
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $groups = Group::where('is_active', true)->get();
        return view('categories.create', compact('groups'));
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
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        $groups = Group::where('is_active', true)->get();
        return view('categories.edit', compact('category', 'groups'));
    }

    public function update(Request $request, Category $category)
    {
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
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Cannot delete category with associated products.');
        }

        $category->delete();
        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
