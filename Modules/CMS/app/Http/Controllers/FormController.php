<?php

namespace Modules\CMS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('cms::forms.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cms::forms.create');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('cms::forms.edit', compact('id'));
    }
}
