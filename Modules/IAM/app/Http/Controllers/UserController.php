<?php

namespace Modules\IAM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('read_users');

        return view('iam::users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create_users');

        return view('iam::users.create');
    }

    public function showPasswordForm(Request $request, User $user)
    {
        // Optionally check if user already has a password
        if ($user->password && ! Hash::needsRehash($user->password)) {
            return redirect()->route('login')->with('warning', 'Password already set.');
        }

        return view('users.set-password', compact('user'));
    }

    public function storePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|confirmed|min:6',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('success', 'Your password has been set. You can now log in.');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $this->authorize('read_users');

        return view('iam::users.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $this->authorize('update_users');

        return view('iam::users.edit', compact('id'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->authorize('delete_users');
        //
    }
}
