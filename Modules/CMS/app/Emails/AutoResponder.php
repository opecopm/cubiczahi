<?php

namespace Modules\CMS\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\CMS\Models\Form;

class AutoResponder extends Mailable
{
    use Queueable, SerializesModels;

    public $form;
    public $data;

    public function __construct(Form $form, $data)
    {
        $this->form = $form;
        $this->data = $data;
    }

    public function build(): self
    {
        $subject = $this->form->auto_responder_template['subject'] ?? 'Thank you for your submission';
        
        return $this->subject($subject)
                    ->view('cms::emails.auto_responder');
    }
}
