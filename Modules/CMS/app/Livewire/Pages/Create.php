<?php

namespace Modules\CMS\Livewire\Pages;

use Livewire\Component;
use Modules\CMS\Models\Page;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $page = [];

    public $breadcrumbImageFile;
    public $videoFile;
    public $iconFile;
    public ?string $breadcrumbUrl = null;
    
    public $activeLocale; // 👈 Current translation language being edited

    public string $aiPrompt = '';

    protected function rules()
    {
        return [
            'page.title.' . $this->activeLocale => 'required|string|min:3|max:255',
            'page.slug' => 'required|string|max:255|unique:cms_pages,slug',
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

    public function removeBreadcrumbImage()
    {
        $this->breadcrumbImageFile = null;
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
            \Illuminate\Support\Facades\Log::error('CMS Page Create Validation failed: ' . json_encode($e->errors()));
            session()->flash('error', 'Validation failed: <br>' . implode('<br>', \Illuminate\Support\Arr::flatten($e->errors())));
            throw $e;
        }

        // Translatable fields — set each locale individually via Spatie's API
        $translatableFields = [
            'title', 'content', 'breadcrumb_title', 'subtitle', 'alternative_title',
            'meta_description', 'meta_keywords', 'og_title', 'og_description',
            'twitter_title', 'twitter_description',
        ];

        // Non-translatable scalar fields only
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('cms_pages');
        $scalarData = array_filter(
            array_intersect_key($this->page, array_flip($columns)),
            fn($v) => !is_array($v)
        );

        // Instantiate model and fill scalar fields
        $page = new Page();
        $page->fill($scalarData);

        // Set each translatable field per locale using Spatie's setTranslation()
        // This guarantees the JSON column is always populated (avoids NOT NULL issues)
        foreach ($translatableFields as $field) {
            if (isset($this->page[$field]) && is_array($this->page[$field])) {
                foreach ($this->page[$field] as $locale => $value) {
                    $page->setTranslation($field, $locale, $value ?? '');
                }
            }
        }

        $page->save();

        if ($this->breadcrumbImageFile) {
            $page->addMedia($this->breadcrumbImageFile->getRealPath())
                ->usingFileName($this->breadcrumbImageFile->getClientOriginalName())
                ->toMediaCollection('breadcrumb_image');
        }

        if ($this->videoFile) {
            $page->addMedia($this->videoFile->getRealPath())
                ->usingFileName($this->videoFile->getClientOriginalName())
                ->toMediaCollection('video_file');
        }

        if ($this->iconFile) {
            $page->addMedia($this->iconFile->getRealPath())
                ->usingFileName($this->iconFile->getClientOriginalName())
                ->toMediaCollection('icon_image');
        }

        session()->flash('message', 'Page created successfully.');
        return redirect()->route('admin.cms.pages.index');
    }

    public function mount()
    {
        $activeLanguages = \Modules\Global\Models\Language::where('status', 'active')->get();
        $this->activeLocale = $activeLanguages->where('is_default', true)->first()?->code ?? 'en';

        $this->page = [
            'slug' => '',
            'status' => 'draft',
            'template_type' => 'default',
            'template_name' => '',
            'parent_id' => null,
            'published_at' => null,
            'canonical_url' => '',
            'og_url' => '',
            'og_type' => '',
            'og_site_name' => '',
            'og_locale' => '',
            'published_time' => null,
            'modified_time' => null,
            'twitter_card' => '',
            'breadcrumb_image' => null,
            'video_url' => '',
            'icon' => '',
            'page_type' => 'default',
            'is_featured' => 0,
        ];

        // Initialize translatable attributes as arrays
        foreach ([
            'title', 'content', 'breadcrumb_title', 'subtitle', 'alternative_title',
            'meta_description', 'meta_keywords', 'og_title', 'og_description',
            'twitter_title', 'twitter_description'
        ] as $field) {
            $this->page[$field] = [];
            foreach ($activeLanguages as $lang) {
                $this->page[$field][$lang->code] = '';
            }
        }
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

    public function render()
    {
        $parentPages = Page::select('id', 'title')->get();
        $activeLanguages = \Modules\Global\Models\Language::where('status', 'active')->get();

        return view('cms::livewire.pages.create', [
            'parentPages' => $parentPages,
            'activeLanguages' => $activeLanguages
        ]);
    }

    public function generateFromAI(\App\Services\AI\CMSService $cmsService)
    {
        $this->validate([
            'aiPrompt' => 'required|string|min:3'
        ]);

        try {
            $data = $cmsService->generatePageContentAndSeo($this->aiPrompt);

            if ($data) {
                if (isset($data['title'])) {
                    $this->page['title'][$this->activeLocale] = $data['title'];
                    $this->generateSlug();
                }
                if (isset($data['meta_description'])) {
                    $this->page['meta_description'][$this->activeLocale] = $data['meta_description'];
                }
                if (isset($data['meta_keywords'])) {
                    $this->page['meta_keywords'][$this->activeLocale] = $data['meta_keywords'];
                }
                if (isset($data['content']) && $this->page['template_type'] === 'default') {
                    $this->page['content'][$this->activeLocale] = $data['content'];
                    
                    // Trigger TinyMCE update or content editor update
                    $activeLanguages = \Modules\Global\Models\Language::where('status', 'active')->get();
                    $lang = $activeLanguages->where('code', $this->activeLocale)->first();
                    $direction = $lang?->direction ?? 'ltr';

                    $this->dispatch('contentLocaleChanged', [
                        'content' => $data['content'],
                        'direction' => $direction
                    ]);
                }

                $this->aiPrompt = '';
                $this->dispatch('close-ai-modal');
                session()->flash('message', 'Content generated successfully!');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Generation failed: ' . $e->getMessage());
        }
    }
}
