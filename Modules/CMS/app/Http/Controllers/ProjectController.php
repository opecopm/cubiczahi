<?php

namespace Modules\CMS\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\CMS\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        return view('cms::projects.index');
    }

    public function create()
    {
        return view('cms::projects.create');
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        return view('cms::projects.edit', compact('project'));
    }
}
