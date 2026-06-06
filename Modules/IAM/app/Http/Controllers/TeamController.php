<?php

namespace Modules\IAM\Http\Controllers;

use App\Http\Controllers\Controller;

class TeamController extends Controller
{
    public function index()
    {
        return view('iam::teams.index');
    }

    public function show($id)
    {
        return view('iam::teams.show', compact('id'));
    }
}
