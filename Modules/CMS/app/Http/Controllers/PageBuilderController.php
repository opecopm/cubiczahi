<?php

namespace Modules\CMS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CMS\Models\PageBuilderPage;
use Modules\CMS\Models\Page;

class PageBuilderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('cms::page-builder.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cms::page-builder.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // This is handled by the Livewire component
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Check if it's a PageBuilderPage or regular Page
        $pageBuilderPage = PageBuilderPage::find($id);
        $regularPage = Page::find($id);
        
        if (!$pageBuilderPage && !$regularPage) {
            abort(404, 'Page not found');
        }
        
        // Determine which page to use
        $page = $pageBuilderPage ?: $regularPage;
        
        return view('cms::page-builder.show', compact('id', 'page'));
    }

    /**
     * Show the page builder interface for editing.
     */
    public function builder($id)
    {
        // Check if it's a PageBuilderPage or regular Page
        $pageBuilderPage = PageBuilderPage::find($id);
        $regularPage = Page::find($id);
        
        if (!$pageBuilderPage && !$regularPage) {
            abort(404, 'Page not found');
        }
        
        // Determine which page to use
        $page = $pageBuilderPage ?: $regularPage;
        
        return view('cms::page-builder.builder', compact('id', 'page'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('cms::page-builder.builder', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // This is handled by the Livewire component
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // This is handled by the Livewire component
    }

    /**
     * Preview the page as it would appear on the frontend.
     */
    public function preview($id)
    {
        // Check if it's a PageBuilderPage or regular Page
        $pageBuilderPage = PageBuilderPage::find($id);
        $regularPage = Page::find($id);
        
        if (!$pageBuilderPage && !$regularPage) {
            abort(404, 'Page not found');
        }
        
        // Determine which page to use
        $page = $pageBuilderPage ?: $regularPage;
        
        return view('cms::page-builder.preview', compact('page'));
    }

    /**
     * Export page data as JSON.
     */
    public function export($id)
    {
        $page = PageBuilderPage::findOrFail($id);
        $pageData = $page->getPageStructure();
        
        return response()->json([
            'page' => $page->toArray(),
            'structure' => $pageData->toArray()
        ]);
    }

    /**
     * Import page data from JSON.
     */
    public function import(Request $request)
    {
        $request->validate([
            'json_data' => 'required|json'
        ]);

        $data = json_decode($request->json_data, true);
        
        // Create new page from imported data
        $page = PageBuilderPage::create([
            'name' => $data['page']['name'] . ' (Imported)',
            'slug' => $data['page']['slug'] . '-imported-' . time(),
            'title' => $data['page']['title'] ?? [],
            'meta_description' => $data['page']['meta_description'] ?? [],
            'meta_keywords' => $data['page']['meta_keywords'] ?? [],
            'status' => 'draft',
            'template_type' => 'page_builder'
        ]);

        // Import structure data
        // This would need to be implemented based on the JSON structure
        
        return redirect()->route('admin.cms.page-builder.builder', $page->id)
            ->with('message', 'Page imported successfully.');
    }
}
