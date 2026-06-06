<?php

namespace Modules\CMS\Livewire\Banners;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\CMS\Models\Banner;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    
    // Quick Edit Properties
    public $selectedBannerId;
    public $bannerName;
    public $bannerSlug;
    public $bannerStatus;
    public $showEditModal = false;
    public $isCreationMode = false;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function editBanner($id)
    {
        $banner = Banner::findOrFail($id);
        $this->selectedBannerId = $banner->id;
        $this->bannerName = $banner->getTranslation('name', 'en');
        $this->bannerSlug = $banner->slug;
        $this->bannerStatus = $banner->status ? 'active' : 'inactive';
        $this->isCreationMode = false;
        $this->showEditModal = true;
    }

    public function createNewBanner()
    {
        $this->reset(['selectedBannerId', 'bannerName', 'bannerSlug']);
        $this->bannerStatus = 'active';
        $this->isCreationMode = true;
        $this->showEditModal = true;
    }

    public function updatedBannerName($value)
    {
        $this->bannerSlug = \Illuminate\Support\Str::slug($value);
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['selectedBannerId', 'bannerName', 'bannerSlug', 'bannerStatus', 'isCreationMode']);
    }

    public function updateBanner()
    {
        if ($this->isCreationMode) {
            $this->validate([
                'bannerName' => 'required|string|max:255',
                'bannerSlug' => 'required|string|max:255|unique:cms_banners,slug',
                'bannerStatus' => 'required|in:active,inactive',
            ]);

            $banner = Banner::create([
                'name' => $this->bannerName,
                'slug' => $this->bannerSlug,
                'status' => $this->bannerStatus === 'active' ? 1 : 0,
            ]);

            $this->closeEditModal();
            session()->flash('message', 'Banner created successfully!');
            return redirect()->route('admin.cms.banners.edit', $banner->id);
        } else {
            $this->validate([
                'bannerName' => 'required|string|max:255',
                'bannerSlug' => 'required|string|max:255|unique:cms_banners,slug,' . $this->selectedBannerId,
                'bannerStatus' => 'required|in:active,inactive',
            ]);

            $banner = Banner::findOrFail($this->selectedBannerId);
            $banner->update([
                'name' => $this->bannerName,
                'slug' => $this->bannerSlug,
                'status' => $this->bannerStatus === 'active' ? 1 : 0,
            ]);

            $this->closeEditModal();
            session()->flash('message', 'Banner updated successfully!');
        }
    }

    public function render()
    {
        $banners = Banner::withCount('items') // use items instead of images
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('cms::livewire.banners.index', compact('banners'));
    }
}
