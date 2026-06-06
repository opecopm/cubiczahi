<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SecurityController extends Controller
{
    public function index(): View
    {
        return view('themes.laundry-one.pages.security');
    }
}
