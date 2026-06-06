<?php

namespace Modules\CMS\Livewire\Projects;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\CMS\Models\Project;
use Modules\CMS\Models\ProjectCategory;
use Illuminate\Support\Str;

class Create extends Component
{
    use WithFileUploads;

    public $project_title = [];
    public $short_description = [];
    public $project_description = [];
    
    // Media
    public $icon_class;
    public $icon_image;
    public $main_image;
    // public $breadcrumb_image; 
    public $gallery_images = [];

    // Dates & Status
    public $start_date;
    public $end_date;
    public $is_upcoming = false; // Default to false (0), matching migration
    public $status = 'upcoming'; // Default to upcoming, as per migration
    public $is_active = true; // Default to active

    // Languages
    public $languages = [];
    public $defaultLocale = 'en';

    // Categories & Tags
    public $categories = []; // Hierarchical list
    public $selectedCategories = []; // Pivot selection
    public $tags = ''; // Comma separated string for input
    public $additional_info = []; // Translatable additional info

    // New Category Modal
    public $newCategoryName = [];
    public $newCategoryParentId = null;
    public $flattenedCategories = [];

    public function mount()
    {
        $this->loadLanguages();
        $this->loadCategories();
    }

    public function loadCategories()
    {
        // Load hierarchical categories
        $this->categories = ProjectCategory::whereNull('parent_id')
            ->with(['children' => function($q) {
                $q->where('is_active', true)->with(['children' => function($q2) {
                    $q2->where('is_active', true);
                }]);
            }])
            ->where('is_active', true)
            ->get();
            
        // Also load a flattened list for the "Parent Category" select in modal
        $this->flattenedCategories = ProjectCategory::where('is_active', true)->get();
    }

    public function createCategory()
    {
        $this->validate([
            "newCategoryName.{$this->defaultLocale}" => 'required|string|max:150',
        ]);

        $category = new ProjectCategory();
        $category->is_active = true;
        $category->parent_id = $this->newCategoryParentId ?: null;
        
        foreach (array_keys($this->languages) as $locale) {
            $name = $this->newCategoryName[$locale] ?? '';
            $category->setTranslation('category_name', $locale, $name);
            $category->setTranslation('slug', $locale, Str::slug($name));
        }

        $category->save();

        $this->newCategoryName = [];
        $this->newCategoryParentId = null;
        $this->loadCategories();
        
        // Add to selected categories
        $this->selectedCategories[] = (string)$category->category_id;
        
        $this->dispatch('close-category-modal'); // Dispatch event to close modal
        session()->flash('message', 'Category created successfully!');
    }

    public function create()
    {
        $this->validate([
            "project_title.{$this->defaultLocale}" => 'required|string|max:255',
            "start_date" => 'nullable|date_format:"d - m - Y"',
            "end_date" => 'nullable|date_format:"d - m - Y"|after_or_equal:start_date',
            "status" => 'required|in:upcoming,in_progress,completed',
            "main_image" => 'nullable|image|max:10240', // 10MB max
            // "breadcrumb_image" => 'nullable|image|max:10240',
            "gallery_images.*" => 'nullable|image|max:10240',
        ]);

        $project = new Project();
        
        // Simple fields
        $project->icon_class = $this->icon_class;
        $project->start_date = $this->start_date ? \Carbon\Carbon::createFromFormat('d - m - Y', $this->start_date)->format('Y-m-d') : null;
        $project->end_date = $this->end_date ? \Carbon\Carbon::createFromFormat('d - m - Y', $this->end_date)->format('Y-m-d') : null;
        $project->is_upcoming = $this->is_upcoming;
        $project->status = $this->status;
        $project->is_active = $this->is_active;
        $project->created_by = auth()->id();
        // $project->category_id = $this->category_id; // Deprecated in favor of pivot
        
        // Tags - convert comma separated string to array
        $project->tags = !empty($this->tags) ? array_map('trim', explode(',', $this->tags)) : null;

        // Translations
        $locales = array_keys($this->languages);
        foreach ($locales as $locale) {
            $project->setTranslation('project_title', $locale, $this->project_title[$locale] ?? '');
            $project->setTranslation('short_description', $locale, $this->short_description[$locale] ?? '');
            $project->setTranslation('project_description', $locale, $this->project_description[$locale] ?? '');
            $project->setTranslation('additional_info', $locale, $this->additional_info[$locale] ?? '');
        }

        // File Uploads
        if ($this->icon_image) {
             $project->icon_image = $this->icon_image->store('projects/icons', 'public');
        }

        if ($this->main_image) {
            $project->main_image = $this->main_image->store('projects/main', 'public');
        }
        


        if (!empty($this->gallery_images)) {
            $galleryPaths = [];
            foreach ($this->gallery_images as $image) {
                $galleryPaths[] = $image->store('projects/gallery', 'public');
            }
            $project->gallery_images = $galleryPaths;
        }

        $project->save();

        if (!empty($this->selectedCategories)) {
            $project->categories()->sync($this->selectedCategories);
        }

        session()->flash('message', 'Project created successfully!');
        return redirect()->route('admin.cms.projects.index');
    }

    public function loadLanguages()
    {
        // specific languages as requested
        $this->languages = [
            'en' => 'English',
            'ar' => 'Arabic',
            'ur' => 'Urdu',
        ];
        $this->defaultLocale = config('app.locale', 'en');
    }

    public function render()
    {
        return view('cms::livewire.projects.create');
    }
}
