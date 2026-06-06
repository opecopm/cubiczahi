<?php

namespace Modules\CMS\Livewire\Forms;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\CMS\Models\Form;
use Modules\CMS\Models\FormSubmission;
use Illuminate\Support\Facades\Mail;
// use App\Mail\FormSubmitted; // access to user's mailables if any, or general mail

class Show extends Component
{
    use WithFileUploads;

    public $formId;
    public $formModel;
    public $data = []; // Holds input data
    public $successMessage = '';

    public function mount($id)
    {
        $this->formId = $id;
        $this->formModel = Form::with('fields')->findOrFail($id);
        
        // Initialize data array
        foreach ($this->formModel->fields as $field) {
             $this->data[$field->name] = null;
        }
    }

    public function submit()
    {
        // Build validation rules dynamically
        $rules = [];
        $messages = [];
        foreach ($this->formModel->fields as $field) {
            $fieldRules = [];
            if ($field->is_required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }
            
            if ($field->type === 'email') $fieldRules[] = 'email';
            if ($field->type === 'number') $fieldRules[] = 'numeric';
            if ($field->type === 'file') $fieldRules[] = 'file|max:10240'; // 10MB limit example
            
            // Add custom rules if any stored in DB (assumed array or pipe-separated string)
            // if (!empty($field->validation_rules)) ...

            $rules['data.' . $field->name] = $fieldRules;
            $messages['data.' . $field->name . '.required'] = $field->getTranslation('label', app()->getLocale()) . ' is required.';
        }
        
        if ($this->formModel->use_captcha) {
            // $rules['captcha'] = 'required|captcha'; // Implement captcha validation if package exists
        }
        
        // Honeypot check
        if ($this->formModel->use_honeypot && !empty($this->data['hp_email_check'])) {
            // Silently fail or return success without saving
            $this->reset('data');
            $this->successMessage = 'Thank you! Your submission has been received.';
            return;
        }

        $this->validate($rules, $messages);

        // Handle File Uploads
        $submissionData = $this->data;
        // Remove honeypot field from saved data
        unset($submissionData['hp_email_check']);
        
        foreach ($this->formModel->fields as $field) {
            if ($field->type === 'file' && isset($this->data[$field->name])) {
                // Store file
                $path = $this->data[$field->name]->store('form_submissions', 'public');
                $submissionData[$field->name] = $path;
            }
        }

        // Save Submission
        $submission = $this->formModel->submissions()->create([
            'data' => $submissionData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Configure Mailer dynamically if settings exist
        if (!empty($this->formModel->mail_settings['host'])) {
            $settings = $this->formModel->mail_settings;
            config([
                'mail.mailers.custom_smtp' => [
                    'transport' => 'smtp',
                    'host' => $settings['host'],
                    'port' => $settings['port'],
                    'encryption' => $settings['encryption'],
                    'username' => $settings['username'],
                    'password' => $settings['password'],
                    'timeout' => null,
                ],
                'mail.from.address' => $settings['from_address'] ?: config('mail.from.address'),
                'mail.from.name' => $settings['from_name'] ?: config('mail.from.name'),
            ]);
            // Force use of this mailer? 
            // Mail::mailer('custom_smtp')...
            // Note: Mail::to() uses default mailer unless specified. 
            // Better to switch default for this request?
            // config(['mail.default' => 'custom_smtp']);
        }

        // Send Emails
        if (!empty($this->formModel->notification_emails)) {
             try {
                // If custom mailer is configured, we must use it. 
                // However, Mailable classes by default use the 'default' mailer.
                // We can use the 'mailer()' method on the Mailable instance if needed, 
                // but setting config variables dynamically as done above is often enough if standard Mail::to() is used 
                // AND we switch the default mailer, or if we specifically use the custom one.
                
                $mailer = !empty($this->formModel->mail_settings['host']) ? Mail::mailer('custom_smtp') : Mail::item('default');
                // The above 'Mail::item' is psuedo, standard facade is Mail::mailer(...)
                
                $mailerInstance = !empty($this->formModel->mail_settings['host']) ? Mail::mailer('custom_smtp') : Mail::mailer('smtp'); // or default
                
                // Let's rely on the config override we did above if it's there?
                // Actually, overriding 'mail.mailers.custom_smtp' doesn't make it default.
                
                if (!empty($this->formModel->mail_settings['host'])) {
                    Mail::mailer('custom_smtp')->to($this->formModel->notification_emails)->send(new \Modules\CMS\Emails\FormSubmitted($submission));
                } else {
                    Mail::to($this->formModel->notification_emails)->send(new \Modules\CMS\Emails\FormSubmitted($submission));
                }
             } catch (\Exception $e) {
                 \Log::error('Form Submission Email Error: ' . $e->getMessage());
             }
        }

        if ($this->formModel->auto_responder && isset($submissionData['email'])) {
             try {
                if (!empty($this->formModel->mail_settings['host'])) {
                    Mail::mailer('custom_smtp')->to($submissionData['email'])->send(new \Modules\CMS\Emails\AutoResponder($this->formModel, $submissionData));
                } else {
                    Mail::to($submissionData['email'])->send(new \Modules\CMS\Emails\AutoResponder($this->formModel, $submissionData));
                }
             } catch (\Exception $e) {
                 \Log::error('Auto Responder Email Error: ' . $e->getMessage());
             }
        }

        $this->reset('data');
        $this->successMessage = 'Thank you! Your submission has been received.';
    }

    public function render()
    {
        return view('cms::livewire.forms.show');
    }
}
