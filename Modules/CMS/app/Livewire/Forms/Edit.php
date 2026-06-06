<?php

namespace Modules\CMS\Livewire\Forms;

use Livewire\Component;
use Modules\CMS\Models\Form;
use Modules\CMS\Models\FormField;
use Illuminate\Support\Str;

class Edit extends Component
{
    public $formId;
    public $title = [];
    public $description = [];
    public $status;

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

    public function mount($id)
    {
        $form = Form::findOrFail($id);
        $this->formId = $form->id;
        $this->title = $form->getTranslations('title');
        $this->description = $form->getTranslations('description');
        $this->status = $form->status;
        $this->notification_emails = implode(', ', $form->notification_emails ?? []);
        $this->auto_responder = $form->auto_responder;
        $this->use_captcha = $form->use_captcha;
        $this->use_honeypot = $form->use_honeypot;
        
        // Merge defaults with saved settings
        if ($form->mail_settings) {
            $this->mail_settings = array_merge($this->mail_settings, $form->mail_settings);
        }

        if ($form->button_settings) {
            $this->button_settings = array_merge($this->button_settings, $form->button_settings);
        }

        // Load fields
        foreach ($form->fields as $field) {
            $this->fields[] = [
                'id' => $field->id,
                'type' => $field->type,
                'label' => $field->getTranslations('label'),
                'name' => $field->name,
                'placeholder' => $field->getTranslations('placeholder'),
                'help_text' => $field->getTranslations('help_text'),
                'width' => $field->width,
                'is_required' => (bool)$field->is_required,
                'options' => $field->options, // JSON or Array
            ];
        }
    }

    public function addField($type = 'text')
    {
        $this->fields[] = [
            'id' => null, // New field
            'type' => $type,
            'label' => [], 
            'name' => '',
            'placeholder' => [], 
            'help_text' => [],
            'width' => '12',
            'is_required' => false,
            'options' => [],
        ];
    }

    public function removeField($index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields); 
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
            $this->fields[$index] = $this->fields[$index - 1];
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

        $form = Form::findOrFail($this->formId);
        $form->update([
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

        // Handle Fields
        $currentFieldIds = array_filter(array_column($this->fields, 'id'));
        // Delete removed fields
        $form->fields()->whereNotIn('id', $currentFieldIds)->delete();

        foreach ($this->fields as $index => $fieldData) {
            $form->fields()->updateOrCreate(
                ['id' => $fieldData['id'] ?? null],
                [
                    'form_id' => $form->id,
                    'type' => $fieldData['type'],
                    'label' => $fieldData['label'],
                    'name' => $fieldData['name'] ?: Str::slug($fieldData['label']['en'] ?? 'field-' . $index),
                    'placeholder' => $fieldData['placeholder'] ?? null,
                    'help_text' => $fieldData['help_text'] ?? null,
                    'width' => $fieldData['width'],
                    'is_required' => $fieldData['is_required'],
                    'options' => $fieldData['options'] ?? null,
                    'order' => $index,
                ]
            );
        }

        return redirect()->route('admin.cms.forms.index')->with('message', 'Form updated successfully.');
    }

    public function render()
    {
        // reusing create view as it is identical in structure
        return view('cms::livewire.forms.create');
    }
}
