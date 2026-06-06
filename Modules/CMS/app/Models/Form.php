<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Form extends Model
{
    use HasFactory, HasTranslations;

    protected $table = 'cms_forms';

    public $translatable = ['title', 'description'];

    protected $fillable = [
        'title',
        'description',
        'status',
        'button_settings',
        'mail_settings',
        'notification_emails',
        'email_template',
        'auto_responder',
        'auto_responder_template',
        'use_captcha',
        'use_honeypot',
    ];

    protected $casts = [
        'button_settings' => 'array',
        'mail_settings' => 'array',
        'notification_emails' => 'array',
        'email_template' => 'array',
        'auto_responder_template' => 'array',
        'auto_responder' => 'boolean',
        'use_captcha' => 'boolean',
        'use_honeypot' => 'boolean',
    ];

    public function fields()
    {
        return $this->hasMany(FormField::class, 'form_id')->orderBy('order');
    }

    public function submissions()
    {
        return $this->hasMany(FormSubmission::class, 'form_id');
    }
}
