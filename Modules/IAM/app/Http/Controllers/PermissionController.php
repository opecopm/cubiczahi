<?php

namespace Modules\IAM\Http\Controllers;

use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    public function index()
    {
        return view('iam::permissions.index');
    }

    public function create()
    {
        return view('iam::permissions.create');
    }
}
