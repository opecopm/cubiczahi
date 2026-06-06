<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Modules\Global\Models\CustomField;

trait HasCustomFields
{
    /**
     * Get custom fields for the current model's module.
     *
     * @param  bool  $showInList  If true, only return fields marked show_in_list
     * @return Collection
     */
    public function modelCustomFields($showInList = false)
    {
        $module = $this->getCustomFieldModule();

        $model = $this->getCustomFieldModel();

        $query = CustomField::forModuleModel($module, $model);

        if ($showInList) {
            $query->visibleInList();
        }

        return $query->get();
    }

    /**
     * Define the module name for custom fields.
     * Each model can override this if needed.
     */
    protected function getCustomFieldModule(): string
    {
        // Extract the second segment from "Modules\Assets\Models\Asset"
        $parts = explode('\\', static::class);

        return $parts[1] ?? class_basename($this);
    }

    protected function getCustomFieldModel(): string
    {
        // Default: class base name (e.g. "Asset")
        return class_basename($this);
    }
}
