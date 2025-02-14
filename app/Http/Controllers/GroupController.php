<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::latest()->paginate(10);
        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        return view('groups.create');
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
            'is_active' => $request->is_active ?? true
        ]);

        return redirect()->route('groups.index')
            ->with('success', 'Group created successfully.');
    }

    public function edit(Group $group)
    {
        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $request->validate([
            'code' => 'required|max:255|unique:groups,code,' . $group->id,
            'name' => 'required|max:255',
            'is_active' => 'boolean'
        ]);

        $group->update([
            'code' => $request->code,
            'name' => $request->name,
            'is_active' => $request->is_active ?? false
        ]);

        return redirect()->route('groups.index')
            ->with('success', 'Group updated successfully');
    }

    public function destroy(Group $group)
    {
        $group->delete();

        return redirect()->route('groups.index')
            ->with('success', 'Group deleted successfully');
    }
}
