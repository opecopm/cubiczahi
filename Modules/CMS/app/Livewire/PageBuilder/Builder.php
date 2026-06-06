<?php

namespace Modules\CMS\Livewire\PageBuilder;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\CMS\Models\PageBuilderPage;
use Modules\CMS\Models\PageBuilderSection;
use Modules\CMS\Models\PageBuilderRow;
use Modules\CMS\Models\PageBuilderColumn;
use Modules\CMS\Models\PageBuilderBlock;
use Modules\CMS\Models\Page;
use Illuminate\Support\Str;

class Builder extends Component
{
    use WithFileUploads;
    
    public $pageId;
    public $page;
    public $sections = [];
    public $selectedElement = null;
    public $selectedElementType = null;
    public $showSettings = false;
    public $previewMode = false;
    
    // Settings properties
    public $settings = [];
    public $sort_order = 0;
    public $css_classes = '';
    public $custom_css = '';
    public $width = 12;
    public $content = [];
    public $blockType = 'text';
    
    // File upload property
    public $imageFile;
    public $uploading = false;
    
    // Track changes for publish button state
    public $hasChanges = false;
    public $originalData = null;
    public $isNewPage = false;

    // Block types available
    public $availableBlocks = [
        'text' => 'Text Block',
        'heading' => 'Heading Block',
        'animated-heading' => 'Animated Heading Block',
        'image' => 'Image Block',
        'button' => 'Button Block',
        'video' => 'Video Block',
        'spacer' => 'Spacer Block',
        'divider' => 'Divider Block',
        'html' => 'HTML Block'
    ];

    // Column widths available
    public $columnWidths = [2, 4, 6, 8, 10, 12];

    public function mount($id)
    {
        $this->pageId = $id;
        
        // Try to find as PageBuilderPage first, then as regular Page
        $this->page = PageBuilderPage::find($id);
        if (!$this->page) {
            $regularPage = Page::findOrFail($id);
            // Convert regular page to PageBuilderPage
            $this->page = $this->convertToPageBuilderPage($regularPage);
        }
        
        $this->loadPageStructure();
        
        // Check if this is a new page (no sections/content yet)
        $this->isNewPage = empty($this->sections) || (count($this->sections) === 0);
        
        // Store original data for change tracking
        $this->originalData = json_encode($this->sections);
        $this->hasChanges = false;
    }
    
    public function checkForChanges()
    {
        $currentData = json_encode($this->sections);
        $this->hasChanges = ($currentData !== $this->originalData);
        
        // Update isNewPage status based on content
        $this->isNewPage = empty($this->sections) || (count($this->sections) === 0);
    }
    
    public function markAsSaved()
    {
        $this->originalData = json_encode($this->sections);
        $this->hasChanges = false;
        $this->isNewPage = false; // Once saved, it's no longer a new page
    }

    public function loadPageStructure()
    {
        $this->sections = $this->page->getPageStructure()->toArray();
    }

    private function convertToPageBuilderPage($regularPage)
    {
        // Create a new PageBuilderPage from regular Page
        $pageBuilderPage = PageBuilderPage::create([
            'name' => is_array($regularPage->title) ? $regularPage->title['en'] ?? 'Untitled' : $regularPage->title,
            'slug' => $regularPage->slug . '-page-builder',
            'title' => $regularPage->title,
            'meta_description' => $regularPage->meta_description,
            'meta_keywords' => $regularPage->meta_keywords,
            'status' => $regularPage->status,
            'template_type' => 'page_builder',
            'published_at' => $regularPage->published_at
        ]);

        // Create a default section with the existing content
        if ($regularPage->content) {
            $section = PageBuilderSection::create([
                'page_id' => $pageBuilderPage->id,
                'section_type' => 'section',
                'sort_order' => 1,
                'settings' => [
                    'background_color' => '#ffffff',
                    'padding_top' => 50,
                    'padding_bottom' => 50,
                ]
            ]);

            $row = PageBuilderRow::create([
                'section_id' => $section->id,
                'sort_order' => 1,
                'settings' => []
            ]);

            $column = PageBuilderColumn::create([
                'row_id' => $row->id,
                'sort_order' => 1,
                'width' => 12,
                'settings' => []
            ]);

            PageBuilderBlock::create([
                'column_id' => $column->id,
                'block_type' => 'text',
                'sort_order' => 1,
                'content' => ['text' => is_array($regularPage->content) ? $regularPage->content['en'] ?? '' : $regularPage->content],
                'settings' => [
                    'text_color' => '#000000',
                    'font_size' => 16,
                    'text_align' => 'left',
                ]
            ]);
        }

        return $pageBuilderPage;
    }

    // Section Management
    public function addSection()
    {
        $section = PageBuilderSection::create([
            'page_id' => $this->pageId,
            'section_type' => 'section',
            'sort_order' => $this->getNextSectionSortOrder(),
            'settings' => [
                'background_color' => '#ffffff',
                'padding_top' => 50,
                'padding_bottom' => 50,
            ]
        ]);

        $this->loadPageStructure();
        $this->selectElement('section', $section->id);
        $this->checkForChanges();
    }

    public function duplicateSection($sectionId)
    {
        $section = PageBuilderSection::findOrFail($sectionId);
        $newSection = $section->duplicate();
        
        $this->loadPageStructure();
        session()->flash('message', 'Section duplicated successfully.');
        $this->checkForChanges();
    }

    public function deleteSection($sectionId)
    {
        PageBuilderSection::findOrFail($sectionId)->delete();
        $this->loadPageStructure();
        $this->selectedElement = null;
        session()->flash('message', 'Section deleted successfully.');
        $this->checkForChanges();
    }

    public function updateSectionSortOrder($sectionId, $newOrder)
    {
        $section = PageBuilderSection::findOrFail($sectionId);
        $section->updateSortOrder($newOrder);
        $this->loadPageStructure();
    }

    // Row Management
    public function addRow($sectionId)
    {
        $row = PageBuilderRow::create([
            'section_id' => $sectionId,
            'sort_order' => $this->getNextRowSortOrder($sectionId),
            'settings' => []
        ]);

        $this->loadPageStructure();
        $this->selectElement('row', $row->id);
        $this->checkForChanges();
    }

    public function duplicateRow($rowId)
    {
        $row = PageBuilderRow::findOrFail($rowId);
        $newRow = $row->duplicate();
        
        $this->loadPageStructure();
        session()->flash('message', 'Row duplicated successfully.');
    }

    public function deleteRow($rowId)
    {
        PageBuilderRow::findOrFail($rowId)->delete();
        $this->loadPageStructure();
        $this->selectedElement = null;
        session()->flash('message', 'Row deleted successfully.');
    }

    public function updateRowSortOrder($rowId, $newOrder)
    {
        $row = PageBuilderRow::findOrFail($rowId);
        $row->updateSortOrder($newOrder);
        $this->loadPageStructure();
    }

    // Column Management
    public function addColumn($rowId, $width = 12)
    {
        $column = PageBuilderColumn::create([
            'row_id' => $rowId,
            'sort_order' => $this->getNextColumnSortOrder($rowId),
            'width' => $width,
            'settings' => []
        ]);

        $this->loadPageStructure();
        $this->selectElement('column', $column->id);
    }

    public function duplicateColumn($columnId)
    {
        $column = PageBuilderColumn::findOrFail($columnId);
        $newColumn = $column->duplicate();
        
        $this->loadPageStructure();
        session()->flash('message', 'Column duplicated successfully.');
    }

    public function deleteColumn($columnId)
    {
        PageBuilderColumn::findOrFail($columnId)->delete();
        $this->loadPageStructure();
        $this->selectedElement = null;
        session()->flash('message', 'Column deleted successfully.');
    }

    public function updateColumnSortOrder($columnId, $newOrder)
    {
        $column = PageBuilderColumn::findOrFail($columnId);
        $column->updateSortOrder($newOrder);
        $this->loadPageStructure();
    }

    public function updateColumnWidth($columnId, $newWidth)
    {
        $column = PageBuilderColumn::findOrFail($columnId);
        if ($column->updateWidth($newWidth)) {
            $this->loadPageStructure();
            session()->flash('message', 'Column width updated successfully.');
        } else {
            session()->flash('error', 'Total column width cannot exceed 12.');
        }
    }

    // Block Management
    public function addBlock($columnId, $blockType = 'text')
    {
        $block = PageBuilderBlock::create([
            'column_id' => $columnId,
            'block_type' => $blockType,
            'sort_order' => $this->getNextBlockSortOrder($columnId),
            'content' => $this->getDefaultBlockContent($blockType),
            'settings' => $this->getDefaultBlockSettings($blockType)
        ]);

        $this->loadPageStructure();
        $this->selectElement('block', $block->id);
        $this->checkForChanges();
    }

    public function duplicateBlock($blockId)
    {
        $block = PageBuilderBlock::findOrFail($blockId);
        $newBlock = $block->duplicate();
        
        $this->loadPageStructure();
        session()->flash('message', 'Block duplicated successfully.');
    }

    public function deleteBlock($blockId)
    {
        PageBuilderBlock::findOrFail($blockId)->delete();
        $this->loadPageStructure();
        $this->selectedElement = null;
        session()->flash('message', 'Block deleted successfully.');
    }

    public function updateBlockSortOrder($blockId, $newOrder)
    {
        $block = PageBuilderBlock::findOrFail($blockId);
        $block->updateSortOrder($newOrder);
        $this->loadPageStructure();
    }

    // Element Selection
    public function selectElement($type, $id)
    {
        $this->selectedElementType = $type;
        $this->selectedElement = $id;
        $this->showSettings = true;
        
        // Load element data into properties
        $this->loadElementData($type, $id);
        
        // Emit event for JavaScript
        $this->dispatch('settingsOpened');
    }
    
    private function loadElementData($type, $id)
    {
        $model = $this->getElementModel();
        if ($model) {
            // Ensure settings is always an array
            $this->settings = is_array($model->settings) ? $model->settings : (json_decode($model->settings, true) ?? []);
            $this->sort_order = $model->sort_order ?? 0;
            $this->css_classes = is_array($model->css_classes) ? implode(' ', $model->css_classes) : ($model->css_classes ?? '');
            $this->custom_css = is_array($model->custom_css) ? implode(' ', $model->custom_css) : ($model->custom_css ?? '');
            
            if ($type === 'column') {
                $this->width = $model->width ?? 12;
            }
            
            if ($type === 'block') {
                // Ensure content is always an array
                $this->content = is_array($model->content) ? $model->content : (json_decode($model->content, true) ?? []);
                $this->blockType = $model->block_type ?? 'text';
                
                // Ensure animation_settings exists for animated headings
                if ($this->blockType === 'animated-heading' && !isset($this->settings['animation_settings'])) {
                    $this->settings['animation_settings'] = [
                        'loop' => 'false',
                        'direction' => 'normal',
                        'fill_mode' => 'both'
                    ];
                }
                
                // Convert boolean loop values to string for consistency
                if ($this->blockType === 'animated-heading' && isset($this->settings['animation_settings']['loop'])) {
                    if (is_bool($this->settings['animation_settings']['loop'])) {
                        $this->settings['animation_settings']['loop'] = $this->settings['animation_settings']['loop'] ? 'true' : 'false';
                    }
                }
            }
        }
    }

    public function deselectElement()
    {
        $this->selectedElement = null;
        $this->selectedElementType = null;
        $this->showSettings = false;
        
        // Reset properties
        $this->settings = [];
        $this->sort_order = 0;
        $this->css_classes = '';
        $this->custom_css = '';
        $this->width = 12;
        $this->content = [];
        $this->blockType = 'text';
        $this->imageFile = null; // Clear uploaded file
        $this->uploading = false; // Reset uploading state
        
        // Emit event for JavaScript
        $this->dispatch('settingsClosed');
    }

    // Settings Management
    public function updateElementSettings()
    {
        if (!$this->selectedElement || !$this->selectedElementType) {
            session()->flash('error', 'No element selected.');
            return;
        }

        $model = $this->getElementModel();
        if (!$model) {
            session()->flash('error', 'Element not found.');
            return;
        }

        try {
            $updateData = [
                'settings' => $this->settings,
                'sort_order' => $this->sort_order,
                'css_classes' => explode(' ', $this->css_classes),
                'custom_css' => explode(' ', $this->custom_css),
            ];
            
            if ($this->selectedElementType === 'column') {
                $updateData['width'] = $this->width;
            }
            
            if ($this->selectedElementType === 'block') {
                $updateData['content'] = $this->content;
            }
            
            $model->update($updateData);
            $this->loadPageStructure();
            session()->flash('message', 'Settings updated successfully.');
            $this->checkForChanges();
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating settings: ' . $e->getMessage());
        }
    }

    public function updateElementContent()
    {
        if (!$this->selectedElement || $this->selectedElementType !== 'block') {
            return;
        }

        $block = PageBuilderBlock::findOrFail($this->selectedElement);
        $block->update(['content' => $this->content]);
        $this->loadPageStructure();
        session()->flash('message', 'Content updated successfully.');
    }
    
    public function updateTextContent($text)
    {
        if (!$this->selectedElement || $this->selectedElementType !== 'block') {
            return;
        }

        $this->content['text'] = $text;
        $block = PageBuilderBlock::findOrFail($this->selectedElement);
        $block->update(['content' => $this->content]);
        $this->loadPageStructure();
    }
    
    public function uploadImage()
    {
        if (!$this->imageFile) {
            session()->flash('error', 'No file selected for upload.');
            return;
        }

        $this->uploading = true;

        try {
            // Generate unique filename
            $filename = time() . '_' . $this->imageFile->getClientOriginalName();
            
            // Store the file in public storage
            $path = $this->imageFile->storeAs('page-builder/images', $filename, 'public');
            
            // Generate the full URL
            $imageUrl = asset('storage/' . $path);
            
            // Update the content with the uploaded image URL
            $this->content['image_url'] = $imageUrl;
            
            // Update the block in database
            if ($this->selectedElement && $this->selectedElementType === 'block') {
                $block = PageBuilderBlock::findOrFail($this->selectedElement);
                $block->update(['content' => $this->content]);
                $this->loadPageStructure();
            }
            
            // Clear the file input
            $this->imageFile = null;
            $this->uploading = false;
            
            session()->flash('message', 'Image uploaded successfully! URL: ' . $imageUrl);
            
        } catch (\Exception $e) {
            $this->uploading = false;
            session()->flash('error', 'Error uploading image: ' . $e->getMessage());
        }
    }

    // Automatically upload when file is selected
    public function updatedImageFile()
    {
        if ($this->imageFile) {
            $this->uploadImage();
        }
    }

    // Live updates for settings
    public function updatedSettings()
    {
        // Update the specific element's settings in the loaded data
        $this->updateElementInData();
        
        // Save to database immediately for live updates
        if ($this->selectedElement && $this->selectedElementType) {
            $this->saveElementSettings();
        }
    }

    public function updatedWidth()
    {
        // Update column width immediately
        $this->updateElementInData();
        
        // Save to database immediately for live updates
        if ($this->selectedElement && $this->selectedElementType === 'column') {
            $this->saveElementSettings();
        }
    }

    public function updatedContent()
    {
        // Update content immediately
        $this->updateElementInData();
        
        // Save to database immediately for live updates
        if ($this->selectedElement && $this->selectedElementType === 'block') {
            $this->saveElementContent();
        }
    }

    // Individual property updates for better live updates
    public function updated($propertyName)
    {
        // Handle specific property updates
        if (str_starts_with($propertyName, 'settings.')) {
            $this->updatedSettings();
        } elseif (str_starts_with($propertyName, 'content.')) {
            $this->updatedContent();
        } elseif ($propertyName === 'width') {
            $this->updatedWidth();
        }
    }

    private function saveElementSettings()
    {
        try {
            $model = $this->getElementModel();
            if ($model) {
                $updateData = [
                    'settings' => is_array($this->settings) ? $this->settings : [],
                    'sort_order' => $this->sort_order,
                    'css_classes' => explode(' ', $this->css_classes),
                    'custom_css' => explode(' ', $this->custom_css),
                ];
                
                if ($this->selectedElementType === 'column') {
                    $updateData['width'] = $this->width;
                }
                
                $model->update($updateData);
                $this->checkForChanges();
            }
        } catch (\Exception $e) {
            // Silently handle errors for live updates
        }
    }

    private function saveElementContent()
    {
        try {
            if ($this->selectedElementType === 'block') {
                $block = PageBuilderBlock::findOrFail($this->selectedElement);
                $block->update(['content' => is_array($this->content) ? $this->content : []]);
                $this->checkForChanges();
            }
        } catch (\Exception $e) {
            // Silently handle errors for live updates
        }
    }

    private function updateElementInData()
    {
        if (!$this->selectedElement || !$this->selectedElementType) {
            return;
        }

        // Find and update the element in the sections data
        foreach ($this->sections as $sectionIndex => $section) {
            if ($this->selectedElementType === 'section' && $section['id'] == $this->selectedElement) {
                $this->sections[$sectionIndex]['settings'] = $this->settings;
                $this->sections[$sectionIndex]['sort_order'] = $this->sort_order;
                $this->sections[$sectionIndex]['css_classes'] = $this->css_classes;
                $this->sections[$sectionIndex]['custom_css'] = $this->custom_css;
                break;
            }

            foreach ($section['rows'] as $rowIndex => $row) {
                if ($this->selectedElementType === 'row' && $row['id'] == $this->selectedElement) {
                    $this->sections[$sectionIndex]['rows'][$rowIndex]['settings'] = $this->settings;
                    $this->sections[$sectionIndex]['rows'][$rowIndex]['sort_order'] = $this->sort_order;
                    $this->sections[$sectionIndex]['rows'][$rowIndex]['css_classes'] = $this->css_classes;
                    $this->sections[$sectionIndex]['rows'][$rowIndex]['custom_css'] = $this->custom_css;
                    break;
                }

                foreach ($row['columns'] as $columnIndex => $column) {
                    if ($this->selectedElementType === 'column' && $column['id'] == $this->selectedElement) {
                        $this->sections[$sectionIndex]['rows'][$rowIndex]['columns'][$columnIndex]['settings'] = $this->settings;
                        $this->sections[$sectionIndex]['rows'][$rowIndex]['columns'][$columnIndex]['sort_order'] = $this->sort_order;
                        $this->sections[$sectionIndex]['rows'][$rowIndex]['columns'][$columnIndex]['css_classes'] = $this->css_classes;
                        $this->sections[$sectionIndex]['rows'][$rowIndex]['columns'][$columnIndex]['custom_css'] = $this->custom_css;
                        $this->sections[$sectionIndex]['rows'][$rowIndex]['columns'][$columnIndex]['width'] = $this->width;
                        break;
                    }

                    foreach ($column['blocks'] as $blockIndex => $block) {
                        if ($this->selectedElementType === 'block' && $block['id'] == $this->selectedElement) {
                            $this->sections[$sectionIndex]['rows'][$rowIndex]['columns'][$columnIndex]['blocks'][$blockIndex]['settings'] = $this->settings;
                            $this->sections[$sectionIndex]['rows'][$rowIndex]['columns'][$columnIndex]['blocks'][$blockIndex]['sort_order'] = $this->sort_order;
                            $this->sections[$sectionIndex]['rows'][$rowIndex]['columns'][$columnIndex]['blocks'][$blockIndex]['css_classes'] = $this->css_classes;
                            $this->sections[$sectionIndex]['rows'][$rowIndex]['columns'][$columnIndex]['blocks'][$blockIndex]['custom_css'] = $this->custom_css;
                            $this->sections[$sectionIndex]['rows'][$rowIndex]['columns'][$columnIndex]['blocks'][$blockIndex]['content'] = $this->content;
                            break;
                        }
                    }
                }
            }
        }
    }

    // Preview Mode
    public function togglePreviewMode()
    {
        $this->previewMode = !$this->previewMode;
        $this->deselectElement();
    }

    // Save Page
    public function savePage()
    {
        $this->page->update([
            'page_data' => $this->sections,
            'status' => 'draft'
        ]);

        session()->flash('message', 'Page saved successfully.');
        $this->markAsSaved();
    }

    public function publishPage()
    {
        $this->page->update([
            'page_data' => $this->sections,
            'status' => 'published',
            'published_at' => now()
        ]);

        session()->flash('message', 'Page published successfully.');
        $this->markAsSaved();
        
        // Redirect to pages index
        return redirect()->route('admin.cms.pages.index');
    }
    
    public function reorderItems($type, $orderedIds)
    {
        if (!is_array($orderedIds) || empty($orderedIds)) {
            return;
        }

        $models = match($type) {
            'section' => PageBuilderSection::whereIn('id', $orderedIds)->get()->keyBy('id'),
            'row'     => PageBuilderRow::whereIn('id', $orderedIds)->get()->keyBy('id'),
            'column'  => PageBuilderColumn::whereIn('id', $orderedIds)->get()->keyBy('id'),
            'block'   => PageBuilderBlock::whereIn('id', $orderedIds)->get()->keyBy('id'),
            default   => collect(),
        };

        foreach ($orderedIds as $index => $id) {
            if ($models->has($id)) {
                $models[$id]->update(['sort_order' => $index + 1]);
            }
        }

        $this->loadPageStructure();
        $this->checkForChanges();
    }

    // Helper Methods
    private function getNextSectionSortOrder()
    {
        return PageBuilderSection::where('page_id', $this->pageId)->max('sort_order') + 1;
    }

    private function getNextRowSortOrder($sectionId)
    {
        return PageBuilderRow::where('section_id', $sectionId)->max('sort_order') + 1;
    }

    private function getNextColumnSortOrder($rowId)
    {
        return PageBuilderColumn::where('row_id', $rowId)->max('sort_order') + 1;
    }

    private function getNextBlockSortOrder($columnId)
    {
        return PageBuilderBlock::where('column_id', $columnId)->max('sort_order') + 1;
    }

    private function getDefaultBlockContent($blockType)
    {
        switch ($blockType) {
            case 'text':
                return ['text' => '<p>Enter your text here...</p>'];
            case 'heading':
                return [
                    'text' => 'Your Heading Here',
                    'level' => 'h2',
                    'alignment' => 'left'
                ];
            case 'animated-heading':
                return [
                    'text' => 'Animated Heading',
                    'level' => 'h2',
                    'animation_type' => 'fadeIn',
                    'animation_duration' => '1s',
                    'animation_delay' => '0s',
                    'alignment' => 'center',
                    'word_letter_delay' => '0.3s',
                    'word_letter_effect' => 'fade'
                ];
            case 'image':
                return ['image_url' => '', 'alt_text' => ''];
            case 'button':
                return ['button_text' => 'Click Me', 'button_url' => '#', 'target' => '_self'];
            case 'video':
                return ['video_url' => '', 'video_type' => 'youtube'];
            case 'spacer':
                return ['height' => 50];
            case 'divider':
                return ['style' => 'solid', 'color' => '#cccccc'];
            case 'html':
                return ['html' => '<!-- Enter your HTML code here -->'];
            default:
                return [];
        }
    }

    private function getDefaultBlockSettings($blockType)
    {
        switch ($blockType) {
            case 'text':
                return [
                    'text_color' => '#000000',
                    'font_size' => 16,
                    'text_align' => 'left',
                    'font_weight' => 'normal'
                ];
            case 'heading':
                return [
                    'text_color' => '#000000',
                    'font_size' => 32,
                    'text_align' => 'left',
                    'font_weight' => 'bold',
                    'margin_top' => 0,
                    'margin_bottom' => 20,
                    'responsive_font_sizes' => [
                        'mobile' => 24,
                        'tablet' => 28,
                        'desktop' => 32
                    ]
                ];
            case 'animated-heading':
                return [
                    'text_color' => '#000000',
                    'font_size' => 36,
                    'text_align' => 'center',
                    'font_weight' => 'bold',
                    'margin_top' => 0,
                    'margin_bottom' => 30,
                    'responsive_font_sizes' => [
                        'mobile' => 28,
                        'tablet' => 32,
                        'desktop' => 36
                    ],
                    'animation_settings' => [
                        'loop' => 'false',
                        'direction' => 'normal',
                        'fill_mode' => 'both'
                    ]
                ];
            case 'button':
                return [
                    'background_color' => '#007bff',
                    'text_color' => '#ffffff',
                    'border_radius' => 4,
                    'padding' => 10
                ];
            default:
                return [];
        }
    }

    private function getElementModel()
    {
        switch ($this->selectedElementType) {
            case 'section':
                return PageBuilderSection::find($this->selectedElement);
            case 'row':
                return PageBuilderRow::find($this->selectedElement);
            case 'column':
                return PageBuilderColumn::find($this->selectedElement);
            case 'block':
                return PageBuilderBlock::find($this->selectedElement);
            default:
                return null;
        }
    }

    public function render()
    {
        return view('cms::livewire.page-builder.builder');
    }
}
