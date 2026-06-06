<?php

namespace Modules\CMS\Livewire\Pages;

use Livewire\Component;
use Modules\CMS\Models\Page;
use Modules\CMS\Models\PageSection;
use Modules\CMS\Models\PageBlock;
use Modules\CMS\Models\Form;
use Modules\CMS\Models\ProjectCategory;
use Modules\CMS\Models\BlogCategory;
use Livewire\WithFileUploads;

class Show extends Component
{
    use WithFileUploads;

    public $page;
    public $sections;
    public $blocks = [];
    
    // Section properties
    public $sectionId;
    public $sectionTitle;
    public $sectionSubtitle;
    public $sectionDescription;
    public $sectionItemsList = [];
    public $sectionIconType = 'library'; // 'library' or 'upload'
    public $sectionIconClass;

    public $sectionIconImage; // string path now
    public $sectionIconUpload; // file object
    public $sectionImage; // URL for display
    public $sectionImageUpload; // File upload
    public $sectionBackgroundColor;
    public $sectionButtons = []; // array of ['text' => '', 'link' => '', 'style' => 'primary']
    public $sectionSortOrder;
    public $sectionColumnWidth;
    public $sectionBadge;
    public $sectionVideoUrl;
    public $sectionBtnText;
    public $sectionBtnLink;
    public $sectionFormId;

    public $availableForms = [];
    
    // Block properties
    public $blockId;
    public $blockType = 'text'; // Default to text
    public $blockHeading;
    public $blockSubheading;
    public $blockDescription;
    public $blockItemsList = [];
    public $blockSuffix; // For 'stat' type
    public $blockImageUpload; // For 'image' type
    public $blockImageUrl; // Existing image URL
    public $blockIconType = 'library';
    public $blockIconClass;

    public $blockIconImage; // string path
    public $blockIconUpload; // file object
    public $blockBackgroundColor;
    public $blockButtons = [];
    public $blockSortOrder;
    public $blockColumnWidth;
    public $blockBadge;
    public $blockVideoUrl;
    public $blockBtnText;

    public $blockBtnLink;

    public $blockPartnersDisplayMode = 'all';
    public $blockPartnersLimitCount;

    public $blockTestimonialsDisplayMode = 'all';
    public $blockTestimonialsLimitCount;

    // Project Block Properties
    public $blockProjectDisplayMode = 'all'; // all, category, latest
    public $blockProjectCategoryId;
    public $blockProjectLimitCount;
    public $projectCategories = [];

    // Service Listing Block Properties
    public $blockServiceListingDisplayMode = 'all';
    public $blockServiceListingLimitCount;
    
    // Blog Listing Block Properties
    public $blockBlogListingLimitCount;
    public $blockBlogListingCategoryId;
    public $blockBlogListingFeaturedPosition = 'left'; // Default to left
    public $blogCategories = [];

    // Course Listing Block Properties
    public $blockCourseListingLimitCount;
    
    // UI state
    public $showSectionModal = false;
    public $showBlockModal = false;
    public $editingSection = false;
    public $editingBlock = false;
    public $selectedSectionId;

    public function mount($id)
    {
        $this->page = Page::with(['sections.blocks'])->findOrFail($id);
        $this->availableForms = Form::all();
        $this->availableForms = Form::all();
        $this->projectCategories = ProjectCategory::where('is_active', true)->get();
        $this->blogCategories = BlogCategory::where('status', 'active')->get();
        $this->loadSections();
    }

    public function loadSections()
    {
        $this->sections = $this->page->sections;
        $this->blocks = [];
        foreach ($this->sections as $section) {
            $this->blocks[$section->id] = $section->blocks;
        }
    }

    public function render()
    {
        return view('cms::livewire.pages.show');
    }

    // Section CRUD Methods
    public function createSection()
    {
        $this->resetSectionForm();
        $this->showSectionModal = true;
        $this->editingSection = false;
        $this->dispatch('modalOpened');
    }

    public function editSection($sectionId)
    {
        $section = PageSection::findOrFail($sectionId);
        $this->sectionId = $section->id;
        $this->sectionTitle = $section->title;
        $this->sectionSubtitle = $section->subtitle;
        $this->sectionDescription = $section->description;
        $this->sectionItemsList = $section->items_list ?? [];
        $this->sectionIconType = $section->icon_type ?? 'library';
        $this->sectionIconClass = $section->icon_class;
        $this->sectionIconImage = $section->icon_image;
        $this->sectionImage = $section->getFirstMediaUrl('content_image');
        $this->sectionBackgroundColor = $section->background_color;
        $this->sectionButtons = $section->buttons ?? [];
        $this->sectionSortOrder = $section->sort_order;
        $this->sectionColumnWidth = $section->column_width;
        $this->sectionBadge = $section->badge;
        $this->sectionVideoUrl = $section->video_url;
        $this->sectionBtnText = $section->btn_text;
        $this->sectionBtnLink = $section->btn_link;
        $this->sectionFormId = $section->form_id;
        
        $this->showSectionModal = true;
        $this->editingSection = true;
        $this->dispatch('modalOpened');
    }

    public function saveSection()
    {
        $this->validate([
            'sectionTitle' => 'required|string|max:255',
            'sectionSubtitle' => 'nullable|string|max:255',
            'sectionDescription' => 'nullable|string',
            'sectionIconClass' => 'nullable|string|max:255',
            'sectionImageUpload' => 'nullable|image|max:2048',
            'sectionSortOrder' => 'nullable|integer|min:0',
            'sectionButtons.*.text' => 'required|string|max:255',
            'sectionButtons.*.link' => 'required|string|max:255',
            'sectionBadge' => 'nullable|string|max:255',
            'sectionVideoUrl' => 'nullable|string|max:255',
            'sectionBtnText' => 'nullable|string|max:255',
            'sectionBtnLink' => 'nullable|string|max:255',
            'sectionFormId' => 'nullable|exists:cms_forms,id',
        ]);

        $data = [
            'page_id' => $this->page->id,
            'title' => $this->sectionTitle,
            'subtitle' => $this->sectionSubtitle,
            'description' => $this->sectionDescription,
            'items_list' => $this->sectionItemsList,
            'icon_type' => $this->sectionIconType,
            'icon_class' => $this->sectionIconClass,
            'icon_image' => $this->sectionIconImage,
            'background_color' => $this->sectionBackgroundColor,
            'buttons' => $this->sectionButtons,
            'sort_order' => $this->sectionSortOrder ?? 0,
            'column_width' => $this->sectionColumnWidth ?? '12',
            'badge' => $this->sectionBadge,
            'video_url' => $this->sectionVideoUrl,
            'btn_text' => $this->sectionBtnText,
            'btn_link' => $this->sectionBtnLink,
            'form_id' => $this->sectionFormId,
        ];

        if ($this->editingSection) {
            $section = PageSection::findOrFail($this->sectionId);
            $section->update($data);
            if ($this->sectionIconUpload) {
                $section->clearMediaCollection('icon');
                $media = $section->addMedia($this->sectionIconUpload)->toMediaCollection('icon');
                $section->update(['icon_image' => $media->getUrl()]);
            }
            if ($this->sectionImageUpload) {
                $section->addMedia($this->sectionImageUpload)->toMediaCollection('content_image');
            }
            session()->flash('message', 'Section updated successfully!');
        } else {
            $section = PageSection::create($data);
            if ($this->sectionIconUpload) {
                 $media = $section->addMedia($this->sectionIconUpload)->toMediaCollection('icon');
                 $section->update(['icon_image' => $media->getUrl()]);
            }
            if ($this->sectionImageUpload) {
                $section->addMedia($this->sectionImageUpload)->toMediaCollection('content_image');
            }
            session()->flash('message', 'Section created successfully!');
        }

        $this->loadSections();
        $this->closeSectionModal();
    }

    public function deleteSection($sectionId)
    {
        PageSection::findOrFail($sectionId)->delete();
        $this->loadSections();
        session()->flash('message', 'Section deleted successfully!');
    }

    public function toggleSectionStatus($sectionId)
    {
        $section = PageSection::findOrFail($sectionId);
        $section->update(['is_enabled' => !$section->is_enabled]);
        $this->loadSections();
        session()->flash('message', 'Section status updated successfully!');
    }

    public function closeSectionModal()
    {
        $this->showSectionModal = false;
        $this->resetSectionForm();
    }

    public function resetSectionForm()
    {
        $this->sectionId = null;
        $this->sectionTitle = '';
        $this->sectionSubtitle = '';
        $this->sectionDescription = '';
        $this->sectionItemsList = [];
        $this->sectionIconType = 'library';
        $this->sectionIconClass = '';
        $this->sectionIconImage = '';
        $this->sectionIconUpload = null;
        $this->sectionImage = null;
        $this->sectionImageUpload = null;
        $this->sectionBackgroundColor = '';
        $this->sectionButtons = [];
        $this->sectionSortOrder = 0;
        $this->sectionColumnWidth = '12';
        $this->sectionBadge = '';
        $this->sectionVideoUrl = '';
        $this->sectionBtnText = '';
        $this->sectionBtnLink = '';
        $this->sectionFormId = null;
    }

    // Block CRUD Methods
    public function createBlock($sectionId)
    {
        $this->resetBlockForm();
        $this->selectedSectionId = $sectionId;
        $this->showBlockModal = true;
        $this->editingBlock = false;
        $this->dispatch('modalOpened');
    }

    public function editBlock($blockId)
    {
        $this->resetBlockForm(); // Reset state before loading new block
        $block = PageBlock::findOrFail($blockId);
        $this->blockId = $block->id;
        $this->selectedSectionId = $block->page_section_id;
        $this->blockType = $block->type ?? 'text';
        $this->blockHeading = $block->heading;
        $this->blockSubheading = $block->subheading;
        $this->blockDescription = $block->description;
        $items = $block->items_list ?? [];
        $this->blockSuffix = $items['suffix'] ?? '';
        if (isset($items['suffix'])) {
            unset($items['suffix']);
        }

        $this->blockPartnersDisplayMode = $items['partners_display_mode'] ?? 'all';
        $this->blockPartnersLimitCount = $items['partners_limit_count'] ?? null;
        if (isset($items['partners_display_mode'])) unset($items['partners_display_mode']);
        if (isset($items['partners_limit_count'])) unset($items['partners_limit_count']);

        $this->blockTestimonialsDisplayMode = $items['testimonials_display_mode'] ?? 'all';
        $this->blockTestimonialsLimitCount = $items['testimonials_limit_count'] ?? null;
        if (isset($items['testimonials_display_mode'])) unset($items['testimonials_display_mode']);
        if (isset($items['testimonials_limit_count'])) unset($items['testimonials_limit_count']);

        $this->blockProjectDisplayMode = $items['project_display_mode'] ?? 'all';
        $this->blockProjectCategoryId = $items['project_category_id'] ?? null;
        $this->blockProjectLimitCount = $items['project_limit_count'] ?? null;
        if (isset($items['project_display_mode'])) unset($items['project_display_mode']);
        if (isset($items['project_category_id'])) unset($items['project_category_id']);
        if (isset($items['project_limit_count'])) unset($items['project_limit_count']);

        $this->blockServiceListingDisplayMode = $items['service_listing_display_mode'] ?? 'all';
        $this->blockServiceListingLimitCount = $items['service_listing_limit_count'] ?? null;
        if (isset($items['service_listing_display_mode'])) unset($items['service_listing_display_mode']);
        if (isset($items['service_listing_limit_count'])) unset($items['service_listing_limit_count']);

        $this->blockBlogListingLimitCount = $items['blog_listing_limit_count'] ?? null;
        if (isset($items['blog_listing_limit_count'])) unset($items['blog_listing_limit_count']);
        
        $this->blockBlogListingCategoryId = $items['blog_listing_category_id'] ?? null;
        if (isset($items['blog_listing_category_id'])) unset($items['blog_listing_category_id']);

        $this->blockCourseListingLimitCount = $items['course_listing_limit_count'] ?? null;
        if (isset($items['course_listing_limit_count'])) unset($items['course_listing_limit_count']);

        // Logic for extracting pricing fields from items_list on Edit
        if ($this->blockType === 'pricing') {
            // Initialize with defaults but preserve existing items
            $this->blockItemsList = [
                'currency' => $items['currency'] ?? '$',
                'price' => $items['price'] ?? '',
                'period' => $items['period'] ?? '',
                'features_text' => $items['features_text'] ?? '',
            ];
            
            // Merge back any numeric keys (manual list items)
            foreach ($items as $key => $value) {
                if (is_numeric($key)) {
                    $this->blockItemsList[$key] = $value;
                }
            }

            // If legacy description exists but no features_text, maybe migrate? 
            if (empty($this->blockItemsList['features_text']) && !empty($block->description)) {
                $this->blockItemsList['features_text'] = $block->description;
            }

            // ENSURE blockDescription is also set for the editor to show the text
            // For pricing blocks, prioritize features_text over the generic description
            if (!empty($this->blockItemsList['features_text'])) {
                $this->blockDescription = $this->blockItemsList['features_text'];
            } elseif (!empty($block->description)) {
                $this->blockDescription = $block->description;
            }
        } else {
             $this->blockItemsList = array_values($items);
        }
        $this->blockImageUrl = $block->getFirstMediaUrl('content_image');

        $this->blockIconType = $block->icon_type ?? 'library';
        $this->blockIconClass = $block->icon_class;
        $this->blockIconImage = $block->icon_image;
        $this->blockBackgroundColor = $block->background_color;
        $this->blockButtons = $block->buttons ?? [];
        $this->blockSortOrder = $block->sort_order;
        $this->blockColumnWidth = $block->column_width;
        $this->blockBadge = $block->badge;
        $this->blockVideoUrl = $block->video_url;
        $this->blockBtnText = $block->btn_text;
        $this->blockBtnLink = $block->btn_link;
        
        $this->showBlockModal = true;
        $this->editingBlock = true;
        $this->dispatch('modalOpened');
    }

    public function saveBlock()
    {
        $rules = [
            'selectedSectionId' => 'required|exists:page_sections,id',
            'blockSortOrder' => 'nullable|integer|min:0',
            'blockButtons.*.text' => 'required|string|max:255',
            'blockButtons.*.link' => 'required|string|max:255',
            'blockBadge' => 'nullable|string|max:255',
            'blockVideoUrl' => 'nullable|string|max:255',
            'blockBtnText' => 'nullable|string|max:255',
            'blockBtnLink' => 'nullable|string|max:255',
            'blockImageUpload' => 'nullable|image|max:2048',
        ];

        if ($this->blockType === 'heading' || $this->blockType === 'stat' || $this->blockType === 'pricing') {
            $rules['blockHeading'] = 'required|string|max:255';
        }

        if ($this->blockType === 'image') {
            $rules['blockImageUpload'] = $this->editingBlock ? 'nullable|image|max:2048' : 'required|image|max:2048';
        }

        if ($this->blockType === 'business_partners') {
            $rules['blockPartnersDisplayMode'] = 'required|in:all,limit';
             if ($this->blockPartnersDisplayMode === 'limit') {
                 $rules['blockPartnersLimitCount'] = 'required|integer|min:0';
             }
        }

        if ($this->blockType === 'testimonials') {
            $rules['blockTestimonialsDisplayMode'] = 'required|in:all,limit';
             if ($this->blockTestimonialsDisplayMode === 'limit') {
                 $rules['blockTestimonialsLimitCount'] = 'required|integer|min:0';
             }
        }

        if ($this->blockType === 'project') {
            $rules['blockProjectDisplayMode'] = 'required|in:all,category,latest';
            if ($this->blockProjectDisplayMode === 'category') {
                $rules['blockProjectCategoryId'] = 'required|exists:cms_project_categories,category_id';
            }
            if ($this->blockProjectDisplayMode === 'latest' || $this->blockProjectDisplayMode === 'category' || $this->blockProjectDisplayMode === 'all') {
                 $rules['blockProjectLimitCount'] = 'nullable|integer|min:1';
            }
        }

        if ($this->blockType === 'service_listing') {
            $rules['blockServiceListingLimitCount'] = 'nullable|integer|min:0';
        }

        if ($this->blockType === 'blog_listing') {
            $rules['blockBlogListingLimitCount'] = 'nullable|integer|min:0';
            $rules['blockBlogListingCategoryId'] = 'nullable|exists:cms_blog_categories,id';
        }

        if ($this->blockType === 'course_listing') {
            $rules['blockCourseListingLimitCount'] = 'nullable|integer|min:0';
        }

        if ($this->blockType === 'pricing') {
            $rules['blockItemsList.price'] = 'nullable|string|max:50';
            $rules['blockItemsList.period'] = 'nullable|string|max:50';
            $rules['blockItemsList.currency'] = 'nullable|string|max:10';
        }

        $this->validate($rules);

        // Initialize Items List from current state
        $itemsList = $this->blockItemsList;
        
        if ($this->blockType === 'stat') {
            $itemsList['suffix'] = $this->blockSuffix;
        }
        if ($this->blockType === 'business_partners') {
            $itemsList['partners_display_mode'] = $this->blockPartnersDisplayMode;
            if ($this->blockPartnersDisplayMode === 'limit') {
                $itemsList['partners_limit_count'] = $this->blockPartnersLimitCount;
            }
        }
        if ($this->blockType === 'testimonials') {
            $itemsList['testimonials_display_mode'] = $this->blockTestimonialsDisplayMode;
            if ($this->blockTestimonialsDisplayMode === 'limit') {
                $itemsList['testimonials_limit_count'] = $this->blockTestimonialsLimitCount;
            }
        }
        if ($this->blockType === 'project') {
            $itemsList['project_display_mode'] = $this->blockProjectDisplayMode;
            $itemsList['project_limit_count'] = $this->blockProjectLimitCount;
            if ($this->blockProjectDisplayMode === 'category') {
                $itemsList['project_category_id'] = $this->blockProjectCategoryId;
            }
        }
        if ($this->blockType === 'service_listing') {
            $itemsList['service_listing_display_mode'] = $this->blockServiceListingDisplayMode;
            $itemsList['service_listing_limit_count'] = $this->blockServiceListingLimitCount;
        }
        if ($this->blockType === 'blog_listing') {
            $itemsList['blog_listing_limit_count'] = $this->blockBlogListingLimitCount;
            $itemsList['blog_listing_category_id'] = $this->blockBlogListingCategoryId;
        }
        if ($this->blockType === 'course_listing') {
            $itemsList['course_listing_limit_count'] = $this->blockCourseListingLimitCount;
        }

        // Processing for Pricing Block
        if ($this->blockType === 'pricing') {
             // 1. Sync the relayed description immediately
             $text = $this->blockDescription;
             $this->blockItemsList['features_text'] = $text;
             
             // 2. Process features: Check if it's HTML (from Rich Text Editor)
             if (!empty($text)) {
                 $isHtml = strip_tags($text) !== $text;
                 if ($isHtml) {
                     $itemsList['features'] = [];
                 } else {
                     $featuresArray = preg_split('/\r\n|\r|\n/', $text);
                     $featuresArray = array_map('trim', $featuresArray);
                     $featuresArray = array_filter($featuresArray, fn($value) => !is_null($value) && $value !== '');
                     $itemsList['features'] = array_values($featuresArray);
                 }
             } else {
                 $itemsList['features'] = [];
             }
             
             // 3. Auto-generate Badge HTML if currently empty
             if (empty($this->blockBadge)) {
                $currency = $this->blockItemsList['currency'] ?? '$';
                $price = $this->blockItemsList['price'] ?? '';
                $period = $this->blockItemsList['period'] ?? '';
                $this->blockBadge = "<sub>{$currency}</sub>{$price} <span>{$period}</span>";
             }

             // 4. Ensure blockDescription itself is updated for the $data array
             $this->blockDescription = $text;
             
             // 5. Final sync of combined items
             $itemsList = array_merge($itemsList, $this->blockItemsList);
        }

        $data = [
            'page_section_id' => $this->selectedSectionId,
            'type' => $this->blockType,
            'heading' => $this->blockHeading,
            'subheading' => $this->blockSubheading,
            'description' => $this->blockDescription,
            'items_list' => $itemsList,
            'icon_type' => $this->blockIconType,
            'icon_class' => $this->blockIconClass,
            'icon_image' => $this->blockIconImage,
            'background_color' => $this->blockBackgroundColor,
            'buttons' => $this->blockButtons,
            'sort_order' => $this->blockSortOrder ?? 0,
            'column_width' => $this->blockColumnWidth ?? '12',
            'badge' => $this->blockBadge,
            'video_url' => $this->blockVideoUrl,
            'btn_text' => $this->blockBtnText,
            'btn_link' => $this->blockBtnLink,
        ];

        if ($this->editingBlock) {
            $block = PageBlock::findOrFail($this->blockId);
            $block->update($data);
            if ($this->blockImageUpload) {
                $block->addMedia($this->blockImageUpload)->toMediaCollection('content_image');
            }
            if ($this->blockIconUpload) {
                $block->clearMediaCollection('icon');
                $media = $block->addMedia($this->blockIconUpload)->toMediaCollection('icon');
                $block->update(['icon_image' => $media->getUrl()]);
            }
            session()->flash('message', 'Block updated successfully!');
        } else {
            $block = PageBlock::create($data);
            if ($this->blockImageUpload) {
                $block->addMedia($this->blockImageUpload)->toMediaCollection('content_image');
            }
            if ($this->blockIconUpload) {
                 $media = $block->addMedia($this->blockIconUpload)->toMediaCollection('icon');
                 $block->update(['icon_image' => $media->getUrl()]);
            }
            session()->flash('message', 'Block created successfully!');
        }

        $this->loadSections();
        $this->closeBlockModal();
    }

    public function removeSectionIcon()
    {
        if ($this->sectionId) {
            $section = PageSection::find($this->sectionId);
            if ($section) {
                $section->clearMediaCollection('icon');
                $section->update(['icon_image' => null]);
            }
        }
        $this->sectionIconImage = null;
        $this->sectionIconUpload = null; 
    }

    public function removeSectionImage()
    {
        if ($this->sectionId) {
            $section = PageSection::find($this->sectionId);
            if ($section) {
                $section->clearMediaCollection('content_image');
            }
        }
        $this->sectionImage = null;
        $this->sectionImageUpload = null; 
    }

    public function removeBlockIcon()
    {
        if ($this->blockId) {
            $block = PageBlock::find($this->blockId);
            if ($block) {
                $block->clearMediaCollection('icon');
                $block->update(['icon_image' => null]);
            }
        }
        $this->blockIconImage = null;
        $this->blockIconUpload = null;
    }

    public function removeBlockImage()
    {
        if ($this->blockId) {
            $block = PageBlock::find($this->blockId);
            if ($block) {
                $block->clearMediaCollection('content_image');
            }
        }
        $this->blockImageUrl = null;
        $this->blockImageUpload = null;
    }

    public function deleteBlock($blockId)
    {
        PageBlock::findOrFail($blockId)->delete();
        $this->loadSections();
        session()->flash('message', 'Block deleted successfully!');
    }

    public function toggleBlockStatus($blockId)
    {
        $block = PageBlock::findOrFail($blockId);
        $block->update(['is_enabled' => !$block->is_enabled]);
        $this->loadSections();
        session()->flash('message', 'Block status updated successfully!');
    }

    public function closeBlockModal()
    {
        $this->showBlockModal = false;
        $this->selectedSectionId = null;
        $this->resetBlockForm();
    }

    public function resetBlockForm()
    {
        $this->blockId = null;
        // Don't reset selectedSectionId here as it's set before calling this in createBlock
        $this->blockType = 'text';
        $this->blockHeading = '';
        $this->blockSubheading = '';
        $this->blockDescription = '';
        $this->blockItemsList = [];
        $this->blockSuffix = '';
        $this->blockImageUpload = null;
        $this->blockImageUrl = null;
        $this->blockIconType = 'library';
        $this->blockIconClass = '';
        $this->blockIconImage = '';
        $this->blockIconUpload = null;
        $this->blockBackgroundColor = '';
        $this->blockButtons = [];
        if ($this->blockType === 'pricing') {
            // Default pricing button and item list structure
            $this->blockButtons[] = ['text' => 'Choose Plan', 'link' => '#', 'style' => 'primary'];
            $this->blockItemsList = [
                'currency' => '$',
                'price' => '',
                'period' => '',
                'features_text' => '',
            ];
        }
        $this->blockSortOrder = 0;
        $this->blockColumnWidth = '12';
        $this->blockBadge = '';
        $this->blockVideoUrl = '';
        $this->blockBtnText = '';
        $this->blockBtnLink = '';
        $this->blockPartnersDisplayMode = 'all';
        $this->blockPartnersLimitCount = null;
        $this->blockTestimonialsDisplayMode = 'all';
        $this->blockTestimonialsLimitCount = null;
        $this->blockProjectDisplayMode = 'all';
        $this->blockProjectCategoryId = null;
        $this->blockProjectLimitCount = null;
        $this->blockServiceListingDisplayMode = 'all';
        $this->blockServiceListingLimitCount = null;
        $this->blockBlogListingLimitCount = null;
        $this->blockBlogListingCategoryId = null;
        $this->blockCourseListingLimitCount = null;
    }

    public function addButton($type)
    {
        if ($type === 'section') {
            $this->sectionButtons[] = ['text' => '', 'link' => '', 'style' => 'primary'];
        } else {
            $this->blockButtons[] = ['text' => '', 'link' => '', 'style' => 'primary'];
        }
    }

    public function removeButton($type, $index)
    {
        if ($type === 'section') {
            unset($this->sectionButtons[$index]);
            $this->sectionButtons = array_values($this->sectionButtons);
        } else {
            unset($this->blockButtons[$index]);
            $this->blockButtons = array_values($this->blockButtons);
        }
    }

    // Helper methods
    public function addItemToList($type)
    {
        if ($type === 'section') {
            $this->sectionItemsList[] = '';
        } else {
            $this->blockItemsList[] = '';
        }
    }

    public function removeItemFromList($type, $index)
    {
        if ($type === 'section') {
            unset($this->sectionItemsList[$index]);
            $this->sectionItemsList = array_values($this->sectionItemsList);
        } else {
            unset($this->blockItemsList[$index]);
            $this->blockItemsList = array_values($this->blockItemsList);
        }
    }
}
