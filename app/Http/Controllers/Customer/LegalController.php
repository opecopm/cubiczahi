<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class LegalController extends Controller
{
    public function privacy(): View
    {
        return view(theme_view('pages.legal.privacy'));
    }

    public function terms(): View
    {
        return view(theme_view('pages.legal.terms'));
    }

    public function refund(): View
    {
        return view(theme_view('pages.legal.refund'));
    }
}
