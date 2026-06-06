<?php

namespace Modules\CMS\Livewire\Projects;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\CMS\Models\Project;
use Modules\CMS\Models\ProjectCategory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Edit extends Component
{
    use WithFileUploads;

    public $project;
    public $projectId;

    public $project_title = [];
    public $short_description = [];
    public $project_description = [];

    // Media
    public $icon_class;
    public $icon_image; // New upload
    public $existing_icon_image;
    
    public $main_image; // New upload
    public $existing_main_image;

    // Separate from create, we don't have breadcrumb here anymore
    
    public $gallery_images = []; // New uploads
    public $existing_gallery_images = [];

    // Dates & Status
    public $start_date;
    public $end_date;
    public $is_upcoming;
    public $status;
    public $is_active;

    // Languages
    public $languages = [];
    public $defaultLocale = 'en';

    // Categories & Tags
    public $categories = []; // Hierarchical list
    public $selectedCategories = []; // Pivot selection
    public $tags = ''; 
    public $additional_info = []; 

    // New Category Modal
    public $newCategoryName = [];
    public $newCategoryParentId = null;
    public $flattenedCategories = [];

    public function mount($id)
    {
        $this->projectId = $id;
        $this->loadLanguages();
        $this->loadCategories();
        $this->loadProject();
    }

    public function loadLanguages()
    {
         $this->languages = [
            'en' => 'English',
            'ar' => 'Arabic',
            'ur' => 'Urdu',
        ];
        $this->defaultLocale = config('app.locale', 'en');
    }

    public function loadCategories()
    {
        // Load hierarchical categories (Parent -> Children -> Grandchildren)
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
        
        $this->dispatch('close-category-modal'); 
        session()->flash('message', 'Category created successfully!');
    }

    public function loadProject()
    {
        $this->project = Project::with('categories')->findOrFail($this->projectId);

        // Basic Fields
        $this->icon_class = $this->project->icon_class;
        $this->start_date = $this->project->start_date ? $this->project->start_date->format('d - m - Y') : null;
        $this->end_date = $this->project->end_date ? $this->project->end_date->format('d - m - Y') : null;
        $this->is_upcoming = (bool)$this->project->is_upcoming;
        $this->status = $this->project->status;
        $this->is_active = (bool)$this->project->is_active;
        
        // Multi-category selection
        $this->selectedCategories = $this->project->categories->pluck('category_id')->map(fn($id) => (string)$id)->toArray();
        
        $this->tags = $this->project->tags ? implode(', ', $this->project->tags) : '';

        // Existing Media
        $this->existing_icon_image = $this->project->icon_image;
        $this->existing_main_image = $this->project->main_image;

        $this->existing_gallery_images = is_array($this->project->gallery_images) ? $this->project->gallery_images : [];

        // Translations (as before)
        foreach (array_keys($this->languages) as $locale) {
            $this->project_title[$locale] = $this->project->getTranslation('project_title', $locale, false);
            $this->short_description[$locale] = $this->project->getTranslation('short_description', $locale, false);
            $this->project_description[$locale] = $this->project->getTranslation('project_description', $locale, false);
            $this->additional_info[$locale] = $this->project->getTranslation('additional_info', $locale, false);
        }
    }

    public function update()
    {
        $this->validate([
            "project_title.{$this->defaultLocale}" => 'required|string|max:255',
            "start_date" => 'nullable|date_format:"d - m - Y"',
            "end_date" => 'nullable|date_format:"d - m - Y"|after_or_equal:start_date',
            "status" => 'required|in:upcoming,in_progress,completed',
            "main_image" => 'nullable|image|max:10240',
            "gallery_images.*" => 'nullable|image|max:10240',
        ]);

        $project = $this->project;
        $project->icon_class = $this->icon_class;
        $project->start_date = $this->start_date ? \Carbon\Carbon::createFromFormat('d - m - Y', $this->start_date)->format('Y-m-d') : null;
        $project->end_date = $this->end_date ? \Carbon\Carbon::createFromFormat('d - m - Y', $this->end_date)->format('Y-m-d') : null;
        $project->is_upcoming = $this->is_upcoming;
        $project->status = $this->status;
        $project->is_active = $this->is_active;
        // category_id field on project table is deprecated/secondary now, but we can update it if needed.
        // For now, ignoring single category_id.
        $project->tags = !empty($this->tags) ? array_map('trim', explode(',', $this->tags)) : null;

        // Translations (as before)
        // ...

        foreach (array_keys($this->languages) as $locale) {
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
            $galleryPaths = $this->existing_gallery_images; // Start with existing
            foreach ($this->gallery_images as $image) {
                $galleryPaths[] = $image->store('projects/gallery', 'public');
            }
            $project->gallery_images = $galleryPaths;
        }

        $project->save();

        $project->categories()->sync($this->selectedCategories);

        session()->flash('message', 'Project updated successfully!');
    }

     // Gallery Management
    public function removeGalleryImage($index)
    {
        if (isset($this->existing_gallery_images[$index])) {
            // Optional: delete file from storage if you want strict cleanup
            // Storage::disk('public')->delete($this->existing_gallery_images[$index]); 
            unset($this->existing_gallery_images[$index]);
            $this->existing_gallery_images = array_values($this->existing_gallery_images);
            
            // Save immediately to reflect change
            $this->project->gallery_images = $this->existing_gallery_images;
            $this->project->save();
        }
    }

    public function render()
    {
        return view('cms::livewire.projects.edit');
    }
}
