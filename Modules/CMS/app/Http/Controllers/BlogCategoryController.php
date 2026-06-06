<?php

namespace Modules\CMS\Http\Controllers;

use Illuminate\Routing\Controller;

class BlogCategoryController extends Controller
{
    public function index()
    {
        return view('cms::blog-categories.index');
    }
}