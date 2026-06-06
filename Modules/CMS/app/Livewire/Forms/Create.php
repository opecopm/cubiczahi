<?php

namespace Modules\CMS\Livewire\Forms;

use Livewire\Component;
use Modules\CMS\Models\Form;
use Modules\CMS\Models\FormField;
use Illuminate\Support\Str;

class Create extends Component
{
    public $title = [];
    public $description = [];
    public $status = 'active';

    // Settings
    public $notification_emails = ''; 
    public $auto_responder = false;
    public $use_captcha = false;
    public $use_honeypot = false;

    // Button Settings
    public $button_settings = [
        'use_custom_submit' => true,
        'submit_text' => ['en' => 'Submit'],
        'submit_class' => 'btn-primary',
        
        'use_reset' => false,
        'reset_text' => ['en' => 'Reset'],
        'reset_class' => 'btn-secondary',
    ];

    // Mail Settings
    public $mail_settings = [
        'driver' => 'smtp',
        'host' => '',
        'port' => '587',
        'encryption' => 'tls',
        'username' => '',
        'password' => '',
        'from_address' => '',
        'from_name' => '',
    ];

    // Fields Builder
    public $fields = [];

    public function mount()
    {
        // Initialize with one empty field
        $this->addField();
    }

    public function addField($type = 'text')
    {
        $this->fields[] = [
            'type' => $type,
            'label' => [], // Translatable
            'name' => '',
            'placeholder' => [], // Translatable
            'help_text' => [],
            'width' => '12',
            'is_required' => false,
            'options' => [], // For select/radio/checkbox
        ];
    }

    public function removeField($index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields); // Re-index
    }

    public function moveFieldUp($index)
    {
        if ($index > 0) {
            $temp = $this->fields[$index];
            $this->fields[$index] = $this->fields[$index - 1];
            $this->fields[$index - 1] = $temp;
        }
    }

    public function moveFieldDown($index)
    {
        if ($index < count($this->fields) - 1) {
            $temp = $this->fields[$index];
            $this->fields[$index] = $this->fields[$index + 1];
            $this->fields[$index + 1] = $temp;
        }
    }

    public function save()
    {
        $this->validate([
            'title.en' => 'required|string',
            'fields' => 'required|array|min:1',
            'fields.*.label.en' => 'nullable',
            'fields.*.name' => 'required|alpha_dash',
            'button_settings.submit_text.en' => 'required',
        ]);

        // Filter mail settings (remove empty values if default is preferred, or store as is)
        // For security, maybe we should encrypt password? For now, storing as plain JSON as requested.
        
        $form = Form::create([
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'notification_emails' => array_map('trim', explode(',', $this->notification_emails)),
            'auto_responder' => $this->auto_responder,
            'use_captcha' => $this->use_captcha,
            'use_honeypot' => $this->use_honeypot,
            'mail_settings' => $this->mail_settings,
            'button_settings' => $this->button_settings,
        ]);

        foreach ($this->fields as $index => $fieldData) {
            $form->fields()->create([
                'type' => $fieldData['type'],
                'label' => $fieldData['label'],
                'name' => $fieldData['name'] ?: Str::slug($fieldData['label']['en'] ?? 'field-' . $index),
                'placeholder' => $fieldData['placeholder'] ?? null,
                'help_text' => $fieldData['help_text'] ?? null,
                'width' => $fieldData['width'],
                'is_required' => $fieldData['is_required'],
                'options' => $fieldData['options'] ?? null,
                'order' => $index,
            ]);
        }

        return redirect()->route('admin.cms.forms.index')->with('message', 'Form created successfully.');
    }

    public function render()
    {
        return view('cms::livewire.forms.create');
    }
}
