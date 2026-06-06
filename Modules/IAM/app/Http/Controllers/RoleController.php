<?php

namespace Modules\IAM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        return view('iam::roles.index');
    }

    public function create()
    {
        return view('iam::roles.create');
    }

    public function store(Request $request)
    {
        $role = Role::create($request->only('name'));

        return redirect()->route('roles.index');
    }

    public function show($id)
    {
        return view('iam::roles.show', compact('id'));
    }

    public function edit(Role $role)
    {
        return view('iam::roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $role->update($request->only('name'));

        return redirect()->route('roles.index');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('roles.index');
    }
}
