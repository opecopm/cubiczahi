<?php

namespace Modules\CMS\Livewire\Testimonials;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Modules\CMS\Models\Testimonial;

class Create extends Component
{
    use WithFileUploads;

    // Translatable fields
    public $translations = [];

    // Non-translatable
    public $email, $image, $video_url, $video_file;
    public $rating = 0;
    public $featured = 0;
    public $sort_order = 0;
    public $status = 1;

    public $activeLocale; // 👈 Current translation language being edited

    public function mount()
    {
        $activeLanguages = \Modules\Global\Models\Language::where('status', 'active')->get();
        $this->activeLocale = $activeLanguages->where('is_default', true)->first()?->code ?? 'en';

        foreach ($activeLanguages as $lang) {
            $this->translations[$lang->code] = [
                'name' => '', 
                'designation' => '', 
                'company' => '', 
                'website' => '', 
                'location' => '', 
                'phone' => '', 
                'about' => '', 
                'message' => ''
            ];
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
        if ($this->image instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $rules['image'] = 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048';
        } else {
            $rules['image'] = 'nullable|string';
        }
        $rules['video_url'] = 'nullable|url|max:255';
        $rules['video_file'] = 'nullable|mimes:mp4,mov,avi,wmv|max:51200';
        $rules['rating'] = 'nullable|integer|min:1|max:5';
        $rules['featured'] = 'boolean';
        $rules['sort_order'] = 'nullable|integer|min:0';
        $rules['status'] = 'boolean';

        return $rules;
    }


    public function save()
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

        $imagePath = null;
        if ($this->image) {
            if ($this->image instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                $imagePath = $this->image->store('testimonials/images', 'public');
            } else {
                $imagePath = $this->image;
            }
        }
        $videoPath = $this->video_file ? $this->video_file->store('testimonials/videos', 'public') : null;

        // Build array with translatable fields mapped for Spatie
        $data = [
            'email' => $this->email,
            'image' => $imagePath,
            'video_url' => $this->video_url,
            'video_path' => $videoPath,
            'rating' => $this->rating,
            'featured' => $this->featured,
            'sort_order' => $this->sort_order,
            'status' => $this->status,
        ];

        $translatableFields = ['name', 'designation', 'company', 'website', 'location', 'phone', 'about', 'message'];

        foreach ($translatableFields as $field) {
            $values = [];
            foreach ($activeLanguages as $lang) {
                $code = $lang->code;
                $values[$code] = $this->translations[$code][$field] ?? '';
            }
            $data[$field] = $values;
        }

        // Create the testimonial in a single mass assignment call
        Testimonial::create($data);

        session()->flash('success', 'Testimonial created successfully.');
        return redirect()->route('admin.cms.testimonials.index');
    }

    public function render()
    {
        $activeLanguages = \Modules\Global\Models\Language::where('status', 'active')->get();
        return view('cms::livewire.testimonials.create', compact('activeLanguages'));
    }
}
