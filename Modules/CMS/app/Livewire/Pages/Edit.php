<?php

namespace Modules\CMS\Livewire\Pages;

use Livewire\Component;
use Modules\CMS\Models\Page;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public $page;
    public $pageId;

    public $breadcrumbImageFile;
    public $videoFile;
    public $iconFile;
    public ?string $breadcrumbUrl = null;
    public ?string $iconUrl = null;
    
    public $activeLocale; // 👈 Current translation language being edited

    protected function rules()
    {
        return [
            'page.title.' . $this->activeLocale => 'required|string|min:3|max:255',
            'page.slug' => 'required|string|max:255|unique:cms_pages,slug,' . $this->pageId,
            'page.content.' . $this->activeLocale => 'nullable|string',
            'page.status' => 'required|in:draft,published',
            'page.template_type' => 'nullable|in:default,custom,page_builder',
            'page.template_name' => 'nullable|string|max:255',
            'page.parent_id' => 'nullable|integer|exists:cms_pages,id',
            'page.published_at' => 'nullable|date',
            'page.meta_description.' . $this->activeLocale => 'nullable|string|max:255',
            'page.meta_keywords.' . $this->activeLocale => 'nullable|string|max:255',
            'page.canonical_url' => 'nullable|url|max:255',
            'page.og_title.' . $this->activeLocale => 'nullable|string|max:255',
            'page.og_description.' . $this->activeLocale => 'nullable|string|max:1000',
            'page.og_url' => 'nullable|url|max:255',
            'page.og_type' => 'nullable|string|max:50',
            'page.og_site_name' => 'nullable|string|max:255',
            'page.og_locale' => 'nullable|string|max:20',
            'page.published_time' => 'nullable|date',
            'page.modified_time' => 'nullable|date',
            'page.twitter_card' => 'nullable|string|max:50',
            'page.twitter_title.' . $this->activeLocale => 'nullable|string|max:255',
            'page.twitter_description.' . $this->activeLocale => 'nullable|string|max:1000',
            'page.breadcrumb_title.' . $this->activeLocale => 'nullable|string',
            'page.subtitle.' . $this->activeLocale => 'nullable|string',
            'page.alternative_title.' . $this->activeLocale => 'nullable|string',
            'page.breadcrumb_image' => 'nullable|string',
            'page.video_url' => 'nullable|string',
            'page.icon' => 'nullable|string',
            'page.page_type' => 'nullable|string|in:default,service,portfolio,blog',
            'page.is_featured' => 'nullable|boolean',
            'breadcrumbImageFile' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'videoFile' => 'nullable|mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4,video/webm|max:51200',
            'iconFile' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
        ];
    }

    public function mount($id)
    {
        $model = Page::findOrFail($id);
        $this->pageId = $model->id;
        
        $activeLanguages = \Modules\Global\Models\Language::where('status', 'active')->get();
        $this->activeLocale = $activeLanguages->where('is_default', true)->first()?->code ?? 'en';

        $this->page = $model->only([
            'slug', 'status',
            'template_type', 'template_name', 'parent_id', 'published_at',
            'canonical_url', 'og_url', 'og_type', 'og_site_name', 'og_locale',
            'published_time', 'modified_time', 'twitter_card',
            'breadcrumb_image', 'video_url',
            'icon', 'page_type', 'is_featured'
        ]);

        // Load translatable attributes as arrays
        foreach ([
            'title', 'content', 'breadcrumb_title', 'subtitle', 'alternative_title',
            'meta_description', 'meta_keywords', 'og_title', 'og_description',
            'twitter_title', 'twitter_description'
        ] as $field) {
            $this->page[$field] = [];
            foreach ($activeLanguages as $lang) {
                $this->page[$field][$lang->code] = $model->getTranslation($field, $lang->code);
            }
        }

        $this->breadcrumbUrl = $model->getFirstMediaUrl('breadcrumb_image') ?: null;
        $this->iconUrl = $model->getFirstMediaUrl('icon_image') ?: null;
    }

    public function updatedActiveLocale($value)
    {
        $activeLanguages = \Modules\Global\Models\Language::where('status', 'active')->get();
        $lang = $activeLanguages->where('code', $value)->first();
        $direction = $lang?->direction ?? 'ltr';

        $defaultLocale = $activeLanguages->where('is_default', true)->first()?->code ?? 'en';

        // Pre-fill empty translations with the default/existing values for all translatable fields
        $translatableFields = [
            'title', 'content', 'breadcrumb_title', 'subtitle', 'alternative_title',
            'meta_description', 'meta_keywords', 'og_title', 'og_description',
            'twitter_title', 'twitter_description',
        ];

        foreach ($translatableFields as $field) {
            // Also treat empty HTML structure or content containing old @foreach loops as empty
            $isFieldEmpty = empty($this->page[$field][$value]) || 
                            ($field === 'content' && trim(strip_tags($this->page[$field][$value])) === '') ||
                            ($field === 'content' && strpos($this->page[$field][$value], '@foreach') !== false);

            if ($isFieldEmpty) {
                $fallbackValue = $this->page[$field][$defaultLocale] ?? '';
                if (empty($fallbackValue)) {
                    foreach ($this->page[$field] as $locale => $text) {
                        if (!empty($text)) {
                            $fallbackValue = $text;
                            break;
                        }
                    }
                }
                $this->page[$field][$value] = $fallbackValue;
            }
        }

        $this->dispatch('contentLocaleChanged', [
            'content' => $this->page['content'][$value] ?? '',
            'direction' => $direction
        ]);
    }

    public function switchLanguage($newLocale, $currentContent)
    {
        // 1. Sync the current content for the previous locale before switching
        $this->page['content'][$this->activeLocale] = $currentContent;

        // 2. Perform the locale switch
        $this->updatedActiveLocale($newLocale);
        $this->activeLocale = $newLocale;
    }

    public function generateSlug()
    {
        if (!empty($this->page['title'][$this->activeLocale])) {
            $title = $this->page['title'][$this->activeLocale];
            
            // Clean up spaces and generate slug that keeps Arabic/Unicode letters
            $slug = preg_replace('/\s+/u', '-', trim($title));
            $slug = preg_replace('/[^\p{L}\p{N}\-]+/u', '', $slug);
            $slug = mb_strtolower($slug, 'UTF-8');
            
            $this->page['slug'] = $slug;
        }
    }

    public function removeBreadcrumbImage()
    {
        $page = Page::findOrFail($this->pageId);
        $page->clearMediaCollection('breadcrumb_image');

        $this->breadcrumbUrl = null;
        $this->breadcrumbImageFile = null;

        $this->dispatch('notify', type: 'success', message: 'Breadcrumb image removed.');
    }

    public function removeIconImage()
    {
        $page = Page::findOrFail($this->pageId);
        $page->clearMediaCollection('icon_image');

        $this->iconUrl = null;
        $this->iconFile = null;

        $this->dispatch('notify', type: 'success', message: 'Icon image removed.');
    }

    public function save()
    {
        // Normalize empty strings to null to prevent validation failure on nullable integer/date/url fields
        if (is_array($this->page)) {
            foreach ($this->page as $key => $value) {
                if ($value === '' && !is_array($value)) {
                    $this->page[$key] = null;
                }
            }
        }

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('CMS Page Validation failed: ' . json_encode($e->errors()));
            session()->flash('error', 'Validation failed: <br>' . implode('<br>', \Illuminate\Support\Arr::flatten($e->errors())));
            throw $e;
        }

        $page = Page::findOrFail($this->pageId);

        // Translatable fields — use setTranslation() so each locale is saved independently
        // without overwriting other locales already stored in the JSON column.
        $translatableFields = [
            'title', 'content', 'breadcrumb_title', 'subtitle', 'alternative_title',
            'meta_description', 'meta_keywords', 'og_title', 'og_description',
            'twitter_title', 'twitter_description',
        ];

        foreach ($translatableFields as $field) {
            if (isset($this->page[$field]) && is_array($this->page[$field])) {
                foreach ($this->page[$field] as $locale => $value) {
                    $page->setTranslation($field, $locale, $value ?? '');
                }
            }
        }

        // Non-translatable scalar fields — filter to valid schema columns only
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('cms_pages');
        $scalarData = array_filter(
            array_intersect_key($this->page, array_flip($columns)),
            fn($v) => !is_array($v)
        );
        $page->fill($scalarData);
        $page->save();

        if ($this->breadcrumbImageFile) {
            // make sure Page implements HasMedia & has 'breadcrumb_image' collection
            $page->clearMediaCollection('breadcrumb_image');
            $page->addMedia($this->breadcrumbImageFile->getRealPath())
                ->usingFileName($this->breadcrumbImageFile->getClientOriginalName())
                ->toMediaCollection('breadcrumb_image');

            $this->breadcrumbUrl = $page->getFirstMediaUrl('breadcrumb_image');
        }

        if ($this->videoFile) {
            $page->addMedia($this->videoFile->getRealPath())
                ->usingFileName($this->videoFile->getClientOriginalName())
                ->toMediaCollection('video_file');
        }

        if ($this->iconFile) {
            $page->clearMediaCollection('icon_image');
            $page->addMedia($this->iconFile->getRealPath())
                ->usingFileName($this->iconFile->getClientOriginalName())
                ->toMediaCollection('icon_image');
            
            $this->iconUrl = $page->getFirstMediaUrl('icon_image');
        }

        session()->flash('message', 'Page updated successfully.');
        return redirect()->route('admin.cms.pages.index');
    }

    public function render()
    {
        $parentPages = Page::select('id', 'title')->where('id', '!=', $this->pageId)->get();
        $activeLanguages = \Modules\Global\Models\Language::where('status', 'active')->get();

        return view('cms::livewire.pages.edit', [
            'parentPages' => $parentPages,
            'activeLanguages' => $activeLanguages
        ]);
    }
}
