<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $data = [];
        $data['roles'] = Role::latest()->get();

        return $data;
    }
    public function store()
    {
        request()->validate([
            'name' => 'required',
        ]);
        return $role = Role::create([
            'name' => request('name'),
            'description' => request('description'),
        ]);
    }
    public function update(Role $role)
    {
        request()->validate([
            'name' => 'required',
        ]);
        $role->update([
            'name' => request('name'),
            'description' => request('description'),
        ]);

        return $role;
    }
    public function destroy(Role $role)
    {
        $role->delete();
        return response()->noContent();
    }
    public function changeStatus(Role $role)
    {
        $role->status = request('status');
        $role->save();
        return response()->json(['success', true]);
    }
    public function search()
    {
        $searchQuery = request('query');
        $roles = Role::where('name', 'like', "%{$searchQuery}%")->get();
        return response()->json($roles);
    }
}
