<?php

namespace Modules\CMS\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\CMS\Models\Team;

class TeamController extends Controller
{
    public function index()
    {
        return view('cms::teams.index');
    }

    public function create()
    {
        return view('cms::teams.create');
    }

    public function edit($id)
    {
        $team = Team::findOrFail($id);
        return view('cms::teams.edit', compact('team'));
    }


}
