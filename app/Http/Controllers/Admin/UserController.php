<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $data = [];
        $data['users'] = User::with('roles')->latest()->get();
        $data['roles'] = Role::latest()->get();

        return $data;
    }
    public function store()
    {
        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8'
        ]);
        return $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('name')),
        ]);
    }
    public function update(User $user)
    {
        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $user->id,
            'password' => 'sometimes|min:8'
        ]);
        $user->update([
            'name' => request('name'),
            'email' => request('email'),
            'password' => request('password') ? bcrypt(request('password')) : $user->password,
        ]);

        return $user;
    }
    public function destroy(User $user)
    {
        $user->delete();
        return response()->noContent();
    }
    public function changeRole(User $user)
    {
        $user->syncRoles([request('role')]);
        return response()->json(['success', true]);
    }
    public function search()
    {
        $searchQuery = request('query');
        $users = User::with('roles')->where('name', 'like', "%{$searchQuery}%")->get();
        return response()->json($users);
    }
}
