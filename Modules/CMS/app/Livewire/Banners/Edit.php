<?php

namespace Modules\CMS\Livewire\Banners;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use Modules\CMS\Models\Banner;
use Modules\CMS\Models\BannerItem;

class Edit extends Component
{
    use WithFileUploads;

    public $banner;

    public $name;
    public $slug;
    public $status;
    public $items = [];

    public $manualSlug = false; // 👈 Track manual slug edits
    public $selectedItemIndex = 0; // 👈 Track selected slide index
    public $activeLocale; // 👈 Current translation language being edited

    public function mount(Banner $banner)
    {
        $this->banner  = $banner;
        $this->name    = $banner->name;
        $this->slug    = $banner->slug;
        $this->status  = $banner->status ? 'active' : 'inactive';

        $activeLanguages = \Modules\Global\Models\Language::where('status', 'active')->get();
        $this->activeLocale = $activeLanguages->where('is_default', true)->first()?->code ?? 'en';

        // Load existing banner items
        foreach ($banner->items as $item) {
            $itemTitle = [];
            $itemSubtitle = [];
            $itemContent = [];

            foreach ($activeLanguages as $lang) {
                $code = $lang->code;
                $itemTitle[$code] = $item->getTranslation('title', $code);
                $itemSubtitle[$code] = $item->getTranslation('subtitle', $code);
                $itemContent[$code] = $item->getTranslation('content', $code);
            }

            // Load existing buttons and ensure label translations are initialized
            $buttons = [];
            foreach ($item->buttons ?? [] as $btn) {
                $labelTranslations = [];
                foreach ($activeLanguages as $lang) {
                    $code = $lang->code;
                    if (is_array($btn['label'] ?? null)) {
                        $labelTranslations[$code] = $btn['label'][$code] ?? '';
                    } else {
                        $labelTranslations[$code] = ($code === $this->activeLocale) ? ($btn['label'] ?? '') : '';
                    }
                }

                $buttons[] = [
                    'label'      => $labelTranslations,
                    'url'        => $btn['url'] ?? '',
                    'sort_order' => $btn['sort_order'] ?? 0,
                ];
            }

            $this->items[] = [
                'id'            => $item->id,
                'title'         => $itemTitle,
                'subtitle'      => $itemSubtitle,
                'content'       => $itemContent,
                'image'         => null,
                'image_path'    => $item->image,
                'image_preview' => $item->image ? asset('storage/' . $item->image) : null,
                'link'          => $item->link,
                'buttons'       => $buttons,
                'sort_order'    => $item->sort_order,
                'status'        => $item->status ? 'active' : 'inactive',
            ];
        }

        if (empty($this->items)) {
            $this->addItem();
        }
    }


    public function addItem()
    {
        $activeLanguages = \Modules\Global\Models\Language::where('status', 'active')->get();
        
        $itemTitle = [];
        $itemSubtitle = [];
        $itemContent = [];

        foreach ($activeLanguages as $lang) {
            $code = $lang->code;
            $itemTitle[$code] = '';
            $itemSubtitle[$code] = '';
            $itemContent[$code] = '';
        }

        $this->items[] = [
                'id'            => null,
                'title'         => $itemTitle,
                'subtitle'      => $itemSubtitle,
                'content'       => $itemContent,
                'image'         => null,
                'image_path'    => null,
                'image_preview' => null,
                'link'          => '',
                'buttons'       => [],
                'sort_order'    => 0,
                'status'        => 'active',
        ];
        $this->selectedItemIndex = count($this->items) - 1; // set selection to new slide
    }

    public function removeItem($index)
    {
        if (!empty($this->items[$index]['id'])) {
            BannerItem::find($this->items[$index]['id'])?->delete();
        }
        unset($this->items[$index]);
        $this->items = array_values($this->items);

        if (empty($this->items)) {
            $this->addItem();
        } else {
            $this->selectedItemIndex = min($this->selectedItemIndex, count($this->items) - 1);
        }
    }

    public function addButton($itemIndex)
    {
        $activeLanguages = \Modules\Global\Models\Language::where('status', 'active')->get();
        $labelTranslations = [];
        foreach ($activeLanguages as $lang) {
            $labelTranslations[$lang->code] = '';
        }

        $this->items[$itemIndex]['buttons'][] = [
            'label'      => $labelTranslations,
            'url'        => '',
            'sort_order' => count($this->items[$itemIndex]['buttons']) + 1,
        ];
    }

    public function removeButton($itemIndex, $btnIndex)
    {
        unset($this->items[$itemIndex]['buttons'][$btnIndex]);
        $this->items[$itemIndex]['buttons'] = array_values($this->items[$itemIndex]['buttons']);
    }

    /**
     * Handle image selection from Media Gallery picker
     */
    #[On('mediaSelected')]
    public function handleMediaSelected($payload)
    {
        $usage = $payload['usage'] ?? '';
        if (str_starts_with($usage, 'banner-image-')) {
            $index = (int) str_replace('banner-image-', '', $usage);
            if (isset($this->items[$index]) && !empty($payload['mediaIds'])) {
                $mediaId = $payload['mediaIds'][0];
                $mediaAsset = \Modules\MediaGallery\Models\MediaAsset::find($mediaId);
                if ($mediaAsset) {
                    $mediaItem = $mediaAsset->getFirstMedia('media') ?: $mediaAsset->getFirstMedia();
                    if ($mediaItem) {
                        $this->items[$index]['image_path'] = 'media-content/' . $mediaItem->file_name;
                        $this->items[$index]['image_preview'] = asset($this->items[$index]['image_path']);
                        $this->items[$index]['image'] = null; // Clear manual upload
                    }
                }
            }
        }
    }

    public function update()
    {
        $this->validate([
            'items.*.sort_order' => 'nullable|integer',
            'items.*.status'     => 'required|in:active,inactive',
        ]);


        foreach ($this->items as $item) {
            if (!empty($item['id'])) {
                // Update existing item
                $bannerItem = BannerItem::find($item['id']);
                if ($bannerItem) {
                    $bannerItem->update([
                        'title'      => $item['title'],
                        'subtitle'   => $item['subtitle'],
                        'content'    => $item['content'],
                        'image'      => $item['image'] ? $item['image']->store('banners', 'public') : ($item['image_path'] ?? $bannerItem->image),
                        'link'       => $item['link'],
                        'buttons'    => $item['buttons'],
                        'sort_order' => $item['sort_order'] ?? 0,
                        'status'     => $item['status'] === 'active' ? 1 : 0,
                    ]);
                }
            } else {
                // Create new item
                $this->banner->items()->create([
                    'title'      => $item['title'],
                    'subtitle'   => $item['subtitle'],
                    'content'    => $item['content'],
                    'image'      => $item['image'] ? $item['image']->store('banners', 'public') : ($item['image_path'] ?? null),
                    'link'       => $item['link'],
                    'buttons'    => $item['buttons'],
                    'sort_order' => $item['sort_order'] ?? 0,
                    'status'     => $item['status'] === 'active' ? 1 : 0,
                ]);
            }
        }

        session()->flash('message', 'Banner updated successfully!');
        return redirect()->route('admin.cms.banners.index');
    }

    public function render()
    {
        $activeLanguages = \Modules\Global\Models\Language::where('status', 'active')->get();
        return view('cms::livewire.banners.edit', compact('activeLanguages'));
    }
}
