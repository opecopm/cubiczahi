<?php

namespace Modules\CMS\Livewire\Teams;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\CMS\Models\Team;

class Create extends Component
{
    use WithFileUploads;

    // Translatable fields
    public $translations = [
        'en' => ['name' => '', 'designation' => '', 'bio' => '', 'message' => '', 'phone' => ''],
        'ur' => ['name' => '', 'designation' => '', 'bio' => '', 'message' => '', 'phone' => ''],
        'ar' => ['name' => '', 'designation' => '', 'bio' => '', 'message' => '', 'phone' => ''],
    ];

    // Non-translatable fields
    public $photo;
    public $email;
    public $facebook;
    public $twitter;
    public $linkedin;
    public $instagram;
    public $status = 1;
    public $sort_order = 0;

    protected $rules = [
        'translations.en.name' => 'required|string|max:255',
        'translations.en.designation' => 'nullable|string|max:255',
        'translations.en.bio' => 'nullable|string',
        'translations.en.message' => 'nullable|string',
        'translations.en.phone' => 'nullable|string|max:20',

        'translations.ur.name' => 'nullable|string|max:255',
        'translations.ur.phone' => 'nullable|string|max:20',

        'translations.ar.name' => 'nullable|string|max:255',
        'translations.ar.phone' => 'nullable|string|max:20',

        'photo' => 'nullable|image|max:2048',
        'email' => 'nullable|email|max:255',
        'facebook' => 'nullable|url',
        'twitter' => 'nullable|url',
        'linkedin' => 'nullable|url',
        'instagram' => 'nullable|url',
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function save()
    {
        $this->validate();

        // Upload photo if provided
        $photoPath = $this->photo ? $this->photo->store('teams/photos', 'public') : null;

        // Create team base record
        $team = Team::create([
            'email' => $this->email,
            'photo' => $photoPath,
            'facebook' => $this->facebook,
            'twitter' => $this->twitter,
            'linkedin' => $this->linkedin,
            'instagram' => $this->instagram,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
        ]);

        $locales = ['en', 'ur', 'ar']; // all locales
        $translatableFields = ['name', 'designation', 'bio', 'message', 'phone'];

        foreach ($translatableFields as $field) {
            $values = [];
            foreach ($locales as $locale) {
                $values[$locale] = $this->translations[$locale][$field] ?? '';
            }
            $team->setTranslations($field, $values);
        }

        // Save all translations to DB
        $team->save();

        session()->flash('success', 'Team member created successfully!');
        return redirect()->route('admin.cms.teams.index');
    }


    public function render()
    {
        return view('cms::livewire.teams.create');
    }
}
