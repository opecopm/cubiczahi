<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class FormField extends Model
{
    use HasFactory, HasTranslations;

    protected $table = 'cms_form_fields';

    public $translatable = ['label', 'placeholder', 'help_text', 'options'];

    protected $fillable = [
        'form_id',
        'type',
        'label',
        'name',
        'placeholder',
        'help_text',
        'options',
        'validation_rules',
        'order',
        'width',
        'is_required',
        'conditional_logic',
    ];

    protected $casts = [
        'options' => 'array',
        'validation_rules' => 'array',
        'conditional_logic' => 'array',
        'is_required' => 'boolean',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }
}
