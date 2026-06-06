<?php

namespace Modules\CMS\Livewire\Blogs;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\CMS\Models\Blog;
use Modules\CMS\Models\BlogCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Modules\Global\Models\Language;

class Edit extends Component
{
    use WithFileUploads;

    public $blog;
    public $title = [];
    public $content = [];
    public $excerpt = [];
    public $status; // blog status (draft/published)
    public $categories = []; // all parent categories with children
    public $selectedCategoryIds = []; // checkboxes binding

    public $image;
    public $author_name;
    public $author_image;
    public $published_at;
    public $comments_count = 0;

    // category modal properties (must not collide with blog properties)
    public $cat_name;
    public $cat_slug;
    public $cat_status = true;
    public $isChild = false;
    public $parentId; // selected parent for subcategory

    // flags + tags
    public $allow_comments = false;
    public $allow_pings = false;
    public $tags = [];
    public $newTag = '';
    public $slug = [];

    // dynamic languages
    public $languages = [];
    public $defaultLocale = 'en';

    public function mount($blogId)
    {
        $this->blog = Blog::with('categories')->findOrFail($blogId);

        $this->status = $this->blog->status;
        $this->allow_comments = $this->blog->allow_comments ?? false;
        $this->allow_pings = $this->blog->allow_pings ?? false;
        $this->tags = is_array($this->blog->tags) ? $this->blog->tags : [];
        $this->author_name = $this->blog->author_name;
        $this->author_image = $this->blog->author_image;
        $this->published_at = $this->blog->published_at ? $this->blog->published_at->format('Y-m-d\TH:i') : null;
        $this->comments_count = $this->blog->comments_count ?? 0;

        // load languages first
        $this->loadLanguages();

        // initialize translations for available locales
        $locales = array_keys($this->languages);
        foreach ($locales as $locale) {
            $this->title[$locale] = $this->blog->getTranslation('title', $locale, false);
            $this->content[$locale] = $this->blog->getTranslation('content', $locale, false);
            $this->excerpt[$locale] = $this->blog->getTranslation('excerpt', $locale, false);
            $this->slug[$locale] = $this->blog->getTranslation('slug', $locale, false);
        }

        // categories bound to checkboxes
        $this->selectedCategoryIds = $this->blog->categories->pluck('id')->toArray();

        $this->loadCategories();
    }

    public function loadLanguages()
    {
        $langs = Language::where('status', 'active')
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        if ($langs->isNotEmpty()) {
            $this->languages = $langs->pluck('name', 'code')->toArray();
            $default = $langs->firstWhere('is_default', true);
            $this->defaultLocale = $default?->code ?? config('app.locale', 'en');
        } else {
            $this->languages = ['en' => 'English'];
            $this->defaultLocale = config('app.locale', 'en');
        }
    }

    public function loadCategories()
    {
        $this->categories = BlogCategory::with('children')->whereNull('parent_id')->get();
    }

    public function update()
    {
        $default = $this->defaultLocale ?: config('app.locale', 'en');
        $this->validate([
            "title.$default" => 'required|string|max:255',
            "content.$default" => 'required|string',
            'status' => 'required|in:draft,published',
            "slug.$default" => 'nullable|string|max:255',
        ]);

        // Update main fields
        $this->blog->update([
            'status' => $this->status,
            'allow_comments' => $this->allow_comments,
            'allow_pings' => $this->allow_pings,
            'tags' => $this->tags,
            'author_name' => $this->author_name,
            'author_image' => $this->author_image,
            'published_at' => $this->published_at ? \Carbon\Carbon::parse($this->published_at) : null,
            'comments_count' => $this->comments_count ?? 0,
        ]);

        // Update translations (Spatie)
        $locales = array_keys($this->languages);
        foreach ($locales as $locale) {
            $this->blog->setTranslation('title', $locale, $this->title[$locale] ?? '');
            $this->blog->setTranslation('content', $locale, $this->content[$locale] ?? '');
            $this->blog->setTranslation('excerpt', $locale, $this->excerpt[$locale] ?? '');
            $slugValue = $this->slug[$locale] ?? '';
            if (!$slugValue && !empty($this->title[$locale])) {
                $slugValue = Str::slug($this->title[$locale]);
            }
            $this->blog->setTranslation('slug', $locale, $slugValue);
        }
        $this->blog->save();

        // Image handling (use public disk)
        if ($this->image) {
            if ($this->blog->featured_image) {
                Storage::disk('public')->delete($this->blog->featured_image);
            }
            $this->blog->featured_image = $this->image->store('blogs', 'public');
            $this->blog->save();
        }

        // Sync categories
        $this->blog->categories()->sync(is_array($this->selectedCategoryIds) ? $this->selectedCategoryIds : []);

        session()->flash('message', 'Blog updated successfully!');
        $this->loadCategories();
        $this->dispatch('notify', type: 'success', message: 'Blog updated successfully!');

        return redirect()->route('admin.cms.blogs.index');
    }

    // ----- Category modal actions -----
    public function toggleParent()
    {
        $this->isChild = false;
        $this->parentId = null;
    }

    public function toggleChild()
    {
        $this->isChild = true;
    }

    

    public function saveCategory()
    {
        $rules = [
            'cat_name' => 'required|string|max:255',
            'cat_slug' => 'nullable|string|max:255',
        ];

        if ($this->isChild) {
            $rules['parentId'] = 'required|exists:cms_blog_categories,id';
        }

        $this->validate($rules);

        $new = BlogCategory::create([
            'name' => $this->cat_name,
            'slug' => $this->cat_slug ?: Str::slug($this->cat_name), // ✅ fallback
            'status' => $this->cat_status ? 1 : 0,
            'parent_id' => $this->isChild ? $this->parentId : null,
        ]);

        $this->selectedCategoryIds[] = $new->id;
        $this->reset(['cat_name', 'cat_slug', 'cat_status', 'parentId', 'isChild']);
        $this->loadCategories();
        $this->dispatch('close-modal', id: 'addCategoryModal');
    }


    public function deleteSelectedCategories()
    {
        if (!empty($this->selectedCategoryIds)) {
            BlogCategory::whereIn('id', $this->selectedCategoryIds)->delete();
            $this->selectedCategoryIds = [];
            $this->loadCategories();
            session()->flash('message', 'Selected categories deleted successfully!');
        }
    }

    // tags
    public function addTag()
    {
        $tag = trim($this->newTag);
        if ($tag && !in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }
        $this->newTag = '';
    }

    public function removeTag($index)
    {
        unset($this->tags[$index]);
        $this->tags = array_values($this->tags);
    }

    public function render()
    {
        return view('cms::livewire.blogs.edit');
    }
}
