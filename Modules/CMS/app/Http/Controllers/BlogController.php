<?php
namespace Modules\CMS\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\CMS\Models\Blog;
use Modules\CMS\Models\BlogCategory; // <- use this
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {

        return view('cms::blogs.index');
    }

    public function create()
    {
        // Fetch categories for dynamic display
        $categories = BlogCategory::all(); // <- use BlogCategory here

        return view('cms::blogs.create', compact('categories'));
    }

    public function edit(Blog $blog)
    {
        return view('cms::blogs.edit', compact('blog'));
    }




    public function store(Request $request)
    {
        $request->validate([
            'title.en' => 'required|string|max:255',
            'content.en' => 'required|string',
            'status' => 'required|in:draft,published',
            'category_ids' => 'array',
            'new_category' => 'nullable|string|max:255',
            'excerpt.en' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:2048',
            'tags' => 'nullable|string'
        ]);

        // Create blog
        $blog = new Blog();
        $blog->status = $request->status;
        $blog->allow_comments = $request->boolean('allow_comments', true);
        $blog->allow_pings = $request->boolean('allow_pings', true);
        $blog->tags = $request->filled('tags')
            ? array_map('trim', explode(',', $request->input('tags')))
            : [];

        // Handle translations via Spatie
        foreach (['en', 'ur', 'ar'] as $locale) {
            $blog->setTranslation('title', $locale, data_get($request->title, $locale));
            $blog->setTranslation('content', $locale, data_get($request->content, $locale));
            $blog->setTranslation('excerpt', $locale, data_get($request->excerpt, $locale));
            $blog->setTranslation(
                'slug',
                $locale,
                data_get($request->title, $locale)
                    ? Str::slug(data_get($request->title, $locale))
                    : null
            );
        }

        $blog->save();

        // Handle featured image (use public disk)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('blogs', 'public');
            $blog->featured_image = $path;
            $blog->save();
        }

        // Handle categories
        $categoryIds = $request->category_ids ?? [];

        // Create new category if provided
        if ($request->filled('new_category')) {
            $newCat = BlogCategory::create(['name' => $request->new_category]); // <- use BlogCategory
            $categoryIds[] = $newCat->id;
        }

        $blog->categories()->sync($categoryIds);

        return redirect()->route('admin.cms.blogs.index')->with('message', 'Blog created successfully.');
    }

    public function destroy(Blog $blog)
    {


        // Detach categories
        $blog->categories()->detach();

        // Delete image if exists
        if ($blog->featured_image && Storage::disk('public')->exists($blog->featured_image)) {
            Storage::disk('public')->delete($blog->featured_image);
        }

        // Delete the blog itself
        $blog->delete();

        return redirect()->route('admin.cms.blogs.index')->with('message', 'Blog deleted successfully.');
    }
}
