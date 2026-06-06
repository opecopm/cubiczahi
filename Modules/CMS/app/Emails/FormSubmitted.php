<?php

namespace Modules\CMS\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\CMS\Models\FormSubmission;

class FormSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;

    public function __construct(FormSubmission $submission)
    {
        $this->submission = $submission;
    }

    public function build(): self
    {
        return $this->subject('New Form Submission: ' . $this->submission->form->getTranslation('title', 'en'))
                    ->view('cms::emails.submission');
    }
}
