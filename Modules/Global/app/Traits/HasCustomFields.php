<?php

namespace Modules\Global\Traits;

use Modules\Global\Models\CustomField;
use Modules\Global\Models\CustomFieldValue;

trait HasCustomFields
{
    /**
     * Polymorphic relationship to get custom field values.
     */
    public function customFields($module)
    {
        return CustomField::where('module', $module)->where('model', class_basename($this));
    }

    public function customFieldValues()
    {
        return $this->morphMany(CustomFieldValue::class, 'customizable');
    }

    public function customFieldValuesForModule($module, $model)
    {
        return $this->customFieldValues()->whereHas('customField', function ($query) use ($module) {
            $query->where('module', $module);
        });
    }
}
