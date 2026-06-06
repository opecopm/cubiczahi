<?php

namespace Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TerritoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('crm::territories.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('crm::territories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // handled via Livewire
        return redirect()->route('crm.territories.index');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('crm::territories.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('crm::territories.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // handled via Livewire
        return redirect()->route('crm.territories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // handled via Livewire
        return redirect()->route('crm.territories.index');
    }
}
