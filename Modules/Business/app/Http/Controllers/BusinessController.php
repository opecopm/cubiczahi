<?php

namespace Modules\Business\Http\Controllers;

use App\Http\Controllers\Controller;

class BusinessController extends Controller
{
    public function businessSettings()
    {
        return view('business::businesses.settings');
    }
}
