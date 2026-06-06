<?php

namespace Modules\Business\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        return view('business::locations.index');
    }

    public function create()
    {
        return $this->index();
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.business.locations.index');
    }

    public function show($id)
    {
        return $this->index();
    }

    public function edit($id)
    {
        return $this->index();
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.business.locations.index');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.business.locations.index');
    }
}
