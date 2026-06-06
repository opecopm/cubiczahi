<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    public function index()
    {
        return view('accounting::accounts.index');
    }

    public function create()
    {
        return view('accounting::accounts.create');
    }

    public function show($id)
    {
        return view('accounting::accounts.show', compact('id'));
    }

    public function edit($id)
    {
        return view('accounting::accounts.edit', compact('id'));
    }
}
