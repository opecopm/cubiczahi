<?php

namespace Modules\CMS\Livewire\Testimonials;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Modules\CMS\Models\Testimonial;

class Edit extends Component
{
    use WithFileUploads;

    public $testimonial;

    // Translations array dynamically loaded
    public $translations = [];

    // Non-translatable fields
    public $email;
    public $image;       // Existing image path
    public $newImage;    // New uploaded image
    public $video_url;
    public $video_file;  // New uploaded video
    public $rating;
    public $featured = 0;
    public $sort_order = 0;
    public $status = 1;

    public $activeLocale; // 👈 Current translation language being edited

    public function mount(Testimonial $testimonial)
    {
        $this->testimonial = $testimonial;

        // Load non-translatable fields
        $this->email = $testimonial->email;
        $this->image = $testimonial->image;
        $this->video_url = $testimonial->video_url;
        $this->rating = $testimonial->rating;
        $this->featured = $testimonial->featured;
        $this->sort_order = $testimonial->sort_order;
        $this->status = $testimonial->status;

        $activeLanguages = \Modules\Global\Models\Language::where('status', 'active')->get();
        $this->activeLocale = $activeLanguages->where('is_default', true)->first()?->code ?? 'en';

        // Load translations dynamically for all active locales
        $translatableFields = ['name', 'designation', 'company', 'website', 'location', 'phone', 'about', 'message'];
        foreach ($activeLanguages as $lang) {
            $code = $lang->code;
            $this->translations[$code] = [];
            foreach ($translatableFields as $field) {
                $this->translations[$code][$field] = $testimonial->getTranslation($field, $code) ?? '';
            }
        }
    }

    /**
     * Handle image selection from Media Gallery picker
     */
    #[On('mediaSelected')]
    public function handleMediaSelected($payload)
    {
        $usage = $payload['usage'] ?? '';
        if ($usage === 'testimonial-image' && !empty($payload['mediaIds'])) {
            $mediaId = $payload['mediaIds'][0];
            $mediaAsset = \Modules\MediaGallery\Models\MediaAsset::find($mediaId);
            if ($mediaAsset) {
                $mediaItem = $mediaAsset->getFirstMedia('media') ?: $mediaAsset->getFirstMedia();
                if ($mediaItem) {
                    $this->image = 'media-content/' . $mediaItem->file_name;
                    $this->newImage = null; // Clear manual upload
                }
            }
        }
    }

    protected function rules()
    {
        $activeLanguages = \Modules\Global\Models\Language::where('status', 'active')->get();
        $rules = [];

        foreach ($activeLanguages as $lang) {
            $code = $lang->code;
            $rules["translations.{$code}.name"] = 'required|string|max:255';
            $rules["translations.{$code}.message"] = 'required|string';
            $rules["translations.{$code}.designation"] = 'nullable|string|max:255';
            $rules["translations.{$code}.company"] = 'nullable|string|max:255';
            $rules["translations.{$code}.website"] = 'nullable|url|max:255';
            $rules["translations.{$code}.location"] = 'nullable|string|max:255';
            $rules["translations.{$code}.phone"] = 'nullable|string|max:20';
            $rules["translations.{$code}.about"] = 'nullable|string';
        }

        // Non-translatable fields
        $rules['email'] = 'nullable|email|max:255';
        $rules['newImage'] = 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048';
        $rules['video_url'] = 'nullable|url|max:255';
        $rules['video_file'] = 'nullable|mimes:mp4,mov,avi,wmv|max:51200';
        $rules['rating'] = 'nullable|integer|min:1|max:5';
        $rules['featured'] = 'boolean';
        $rules['sort_order'] = 'nullable|integer|min:0';
        $rules['status'] = 'boolean';

        return $rules;
    }

    public function update()
    {
        $activeLanguages = \Modules\Global\Models\Language::where('status', 'active')->get();
        $defaultLocale = $activeLanguages->where('is_default', true)->first()?->code ?? 'en';

        // Copy default values to other locales if they are empty
        foreach ($activeLanguages as $lang) {
            $code = $lang->code;
            if ($code !== $defaultLocale) {
                foreach ($this->translations[$defaultLocale] as $field => $value) {
                    if (empty($this->translations[$code][$field])) {
                        $this->translations[$code][$field] = $value;
                    }
                }
            }
        }

        $this->validate();

        // Handle new image upload
        if ($this->newImage) {
            if ($this->testimonial->image && str_starts_with($this->testimonial->image, 'testimonials/') && \Storage::disk('public')->exists($this->testimonial->image)) {
                \Storage::disk('public')->delete($this->testimonial->image);
            }
            $this->testimonial->image = $this->newImage->store('testimonials/images', 'public');
        } else {
            $this->testimonial->image = $this->image;
        }

        // Handle new video upload
        if ($this->video_file instanceof \Livewire\TemporaryUploadedFile) {
            if ($this->testimonial->video_path && \Storage::disk('public')->exists($this->testimonial->video_path)) {
                \Storage::disk('public')->delete($this->testimonial->video_path);
            }
            $this->testimonial->video_path = $this->video_file->store('testimonials/videos', 'public');
        }

        // Update non-translatable fields
        $this->testimonial->email = $this->email;
        $this->testimonial->video_url = $this->video_url;
        $this->testimonial->rating = $this->rating;
        $this->testimonial->featured = $this->featured;
        $this->testimonial->sort_order = $this->sort_order;
        $this->testimonial->status = $this->status;

        // Save all translations dynamically
        $translatableFields = ['name', 'designation', 'company', 'website', 'location', 'phone', 'about', 'message'];

        foreach ($translatableFields as $field) {
            $values = [];
            foreach ($activeLanguages as $lang) {
                $code = $lang->code;
                $values[$code] = $this->translations[$code][$field] ?? '';
            }
            $this->testimonial->setTranslations($field, $values);
        }

        // Save once
        $this->testimonial->save();

        session()->flash('success', 'Testimonial updated successfully.');
        return redirect()->route('admin.cms.testimonials.index');
    }

    public function render()
    {
        $activeLanguages = \Modules\Global\Models\Language::where('status', 'active')->get();
        return view('cms::livewire.testimonials.edit', compact('activeLanguages'));
    }
}
