<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('read_items');

        return view('inventory::items.index');
    }

    /**
     * Display a listing of services.
     */
    public function services()
    {
        $this->authorize('read_items');

        return view('inventory::items.services');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create_items');

        return view('inventory::items.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create_items');

        return redirect()->route('inventory.items.index');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $this->authorize('read_items');

        return view('inventory::items.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $this->authorize('update_items');

        return view('inventory::items.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->authorize('update_items');

        return redirect()->route('inventory.items.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $this->authorize('delete_items');

        return redirect()->route('inventory.items.index');
    }
}
