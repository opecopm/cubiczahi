<?php

namespace Modules\CMS\Livewire\Teams;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\CMS\Models\Team;

class Edit extends Component
{
    use WithFileUploads;

    public $teamId;

    // Translatable fields for all languages
    public $translations = [
        'en' => ['name' => '', 'designation' => '', 'bio' => '', 'message' => '', 'phone' => ''],
        'ur' => ['name' => '', 'designation' => '', 'bio' => '', 'message' => '', 'phone' => ''],
        'ar' => ['name' => '', 'designation' => '', 'bio' => '', 'message' => '', 'phone' => ''],
    ];

    // Non-translatable fields
    public $photo, $newPhoto;
    public $status = 1;
    public $sort_order = 0;

    // Social media
    public $facebook, $twitter, $linkedin, $instagram;

    protected $rules = [
        'translations.en.name' => 'required|string|max:255',
        'translations.ur.name' => 'nullable|string|max:255',
        'translations.ar.name' => 'nullable|string|max:255',
        'newPhoto' => 'nullable|image|max:2048',
        'status' => 'boolean',
        'sort_order' => 'nullable|integer|min:0',
        'facebook' => 'nullable|url|max:255',
        'twitter' => 'nullable|url|max:255',
        'linkedin' => 'nullable|url|max:255',
        'instagram' => 'nullable|url|max:255',
    ];

    public function mount($id)
    {
        $team = Team::findOrFail($id);

        $this->teamId = $team->id;
        $this->photo = $team->photo;
        $this->status = $team->status;
        $this->sort_order = $team->sort_order;

        $this->facebook = $team->facebook;
        $this->twitter = $team->twitter;
        $this->linkedin = $team->linkedin;
        $this->instagram = $team->instagram;

        // Load translations for all languages
        foreach (['en','ur','ar'] as $locale) {
            $this->translations[$locale]['name'] = $team->getTranslation('name', $locale);
            $this->translations[$locale]['designation'] = $team->getTranslation('designation', $locale);
            $this->translations[$locale]['bio'] = $team->getTranslation('bio', $locale);
            $this->translations[$locale]['message'] = $team->getTranslation('message', $locale);
            $this->translations[$locale]['phone'] = $team->getTranslation('phone', $locale);
        }
    }

    public function update()
    {
        $this->validate();

        $team = Team::findOrFail($this->teamId);

        // Handle photo update
        if ($this->newPhoto) {
            if ($this->photo && \Storage::disk('public')->exists($this->photo)) {
                \Storage::disk('public')->delete($this->photo);
            }
            $this->photo = $this->newPhoto->store('teams', 'public');
        }

        // Update non-translatable fields
        $team->update([
            'photo'      => $this->photo,
            'status'     => $this->status,
            'sort_order' => $this->sort_order,
            'facebook'   => $this->facebook,
            'twitter'    => $this->twitter,
            'linkedin'   => $this->linkedin,
            'instagram'  => $this->instagram,
        ]);

        // Update translations for all languages
        foreach (['name','designation','bio','message','phone'] as $field) {
            foreach (['en','ur','ar'] as $locale) {
                $team->setTranslation($field, $locale, $this->translations[$locale][$field] ?? '');
            }
        }

        $team->save();

        session()->flash('success', 'Team member updated successfully.');
        return redirect()->route('admin.cms.teams.index');
    }

    public function render()
    {
        return view('cms::livewire.teams.edit');
    }
}
