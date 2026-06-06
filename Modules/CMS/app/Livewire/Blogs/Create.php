<?php

namespace Modules\CMS\Livewire\Blogs;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\CMS\Models\Blog;
use Modules\CMS\Models\BlogCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Modules\Global\Models\Language;

class Create extends Component
{
    use WithFileUploads;

    public $title = [];
    public $content = [];
    public $excerpt = [];
    public $status = 'draft';
    public $categories = []; 
    public $categoryIds = []; // checkboxes binding

    public $image;
    public $author_name;
    public $author_image;
    public $published_at;
    public $comments_count = 0;

    // category modal props
    public $cat_name;
    public $cat_slug;
    public $cat_status = true;
    public $isChild = false;
    public $parentId;

    // flags + tags
    public $allow_comments = false;
    public $allow_pings = false;
    public $tags = [];
    public $newTag = '';

    // dynamic languages
    public $languages = [];
    public $defaultLocale = 'en';

    public function mount()
    {
        $this->loadCategories();
        $this->loadLanguages();
    }

    public function loadCategories()
    {
        $this->categories = BlogCategory::with('children')
            ->whereNull('parent_id')
            ->get();
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

    public function createBlog()
    {
        $default = $this->defaultLocale ?: config('app.locale', 'en');
        $this->validate([
            "title.$default" => 'required|string|max:255',
            "content.$default" => 'required|string',
            'status' => 'required|in:draft,published',
        ]);

        $blog = new Blog();
        $blog->status = $this->status;
        $blog->allow_comments = $this->allow_comments;
        $blog->allow_pings = $this->allow_pings;
        $blog->tags = $this->tags;
        $blog->author_name = $this->author_name;
        $blog->author_image = $this->author_image;
        $blog->published_at = $this->published_at ? \Carbon\Carbon::parse($this->published_at) : null;
        $blog->comments_count = $this->comments_count ?? 0;

        // translations
        $locales = array_keys($this->languages);
        foreach ($locales as $locale) {
            $blog->setTranslation('title', $locale, $this->title[$locale] ?? '');
            $blog->setTranslation('content', $locale, $this->content[$locale] ?? '');
            $blog->setTranslation('excerpt', $locale, $this->excerpt[$locale] ?? '');
            $blog->setTranslation('slug', $locale, !empty($this->title[$locale]) ? Str::slug($this->title[$locale]) : '');
        }

        $blog->save();

        // image upload
        if ($this->image) {
            $blog->featured_image = $this->image->store('blogs', 'public'); // ✅ use public disk
            $blog->save();
        }

        // categories sync
        $blog->categories()->sync(is_array($this->categoryIds) ? $this->categoryIds : []);

        session()->flash('message', 'Blog created successfully!');
        $this->dispatch('notify', type: 'success', message: 'Blog created successfully!');

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
            'slug' => $this->cat_slug ?: Str::slug($this->cat_name),
            'status' => $this->cat_status ? 1 : 0,
            'parent_id' => $this->isChild ? $this->parentId : null,
        ]);

        $this->categoryIds[] = $new->id;
        $this->reset(['cat_name', 'cat_slug', 'cat_status', 'parentId', 'isChild']);
        $this->loadCategories();
        $this->dispatch('close-modal', id: 'addCategoryModal');
    }

    // ----- Tags -----
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
        return view('cms::livewire.blogs.create');
    }
}
