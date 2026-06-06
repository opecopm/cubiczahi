<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ItemCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('read_item_categories');

        return view('inventory::item-categories.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create_item_categories');

        return view('inventory::item-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create_item_categories');

        return redirect()->route('inventory.item-categories.index');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $this->authorize('read_item_categories');

        return view('inventory::item-categories.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $this->authorize('update_item_categories');

        return view('inventory::item-categories.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->authorize('update_item_categories');

        return redirect()->route('inventory.item-categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $this->authorize('delete_item_categories');

        return redirect()->route('inventory.item-categories.index');
    }
}
