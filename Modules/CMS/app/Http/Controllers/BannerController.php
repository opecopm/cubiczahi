<?php

namespace Modules\CMS\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CMS\Models\Banner;
use Modules\CMS\Models\BannerItem;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    /**
     * Display a listing of banners.
     */
    public function index()
    {
        $banners = Banner::with('items')->get();
        return view('cms::banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new banner.
     */
    public function create()
    {
        return view('cms::banners.create');
    }

    /**
     * Store a newly created banner along with its items.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|array',  // translatable
            'status' => 'required|boolean',
            'items' => 'array',
            'items.*.title' => 'required|array',
            'items.*.subtitle' => 'nullable|array',
            'items.*.content' => 'nullable|array',
            'items.*.image' => 'required|string',
            'items.*.link' => 'nullable|string',
            'items.*.buttons' => 'nullable|array',
            'items.*.sort_order' => 'nullable|integer',
            'items.*.status' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($data) {
            $banner = Banner::create([
                'name' => $data['name'],
                'status' => $data['status'],
            ]);

            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $banner->items()->create($item);
                }
            }
        });

        return redirect()->route('admin.cms.banners.index')
                         ->with('success', 'Banner created successfully.');
    }

    /**
     * Show the form for editing a banner.
     */
   public function edit($id)
{
    $banner = Banner::with('items')->findOrFail($id);
    return view('cms::banners.edit', compact('banner'));
}


    /**
     * Update a banner along with its items.
     */
    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'name' => 'required|array',
            'status' => 'required|boolean',
            'items' => 'array',
            'items.*.id' => 'nullable|integer|exists:cms_banner_items,id',
            'items.*.title' => 'required|array',
            'items.*.subtitle' => 'nullable|array',
            'items.*.content' => 'nullable|array',
            'items.*.image' => 'required|string',
            'items.*.link' => 'nullable|string',
            'items.*.buttons' => 'nullable|array',
            'items.*.sort_order' => 'nullable|integer',
            'items.*.status' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($banner, $data) {
            $banner->update([
                'name' => $data['name'],
                'status' => $data['status'],
            ]);

            $existingIds = $banner->items()->pluck('id')->toArray();
            $submittedIds = collect($data['items'])->pluck('id')->filter()->toArray();

            // Delete removed items
            $toDelete = array_diff($existingIds, $submittedIds);
            if (!empty($toDelete)) {
                BannerItem::whereIn('id', $toDelete)->delete();
            }

            // Update or create items
            foreach ($data['items'] as $item) {
                if (isset($item['id'])) {
                    $banner->items()->where('id', $item['id'])->update($item);
                } else {
                    $banner->items()->create($item);
                }
            }
        });

        return redirect()->route('admin.cms.banners.index')
                         ->with('success', 'Banner updated successfully.');
    }

    /**
     * Remove a banner and its items.
     */
    public function destroy(Banner $banner)
    {
        $banner->delete();
        return redirect()->route('admin.cms.banners.index')
                         ->with('success', 'Banner deleted successfully.');
    }
}
