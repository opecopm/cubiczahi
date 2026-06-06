<?php

namespace Modules\Global\Http\Controllers;

use App\Http\Controllers\Controller;

class ReferenceSchemaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('global::reference-schemas.index');
    }
}
