<?php

namespace Modules\Global\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomField extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'module',
        'model',
        'name',
        'type',
        'options',
        'is_required',
        'show_in_list',
    ];

    protected $dates = ['deleted_at'];

    public static function moduleSelect(): array
    {
        $path = base_path('modules_statuses.json'); // file in project root

        if (file_exists($path)) {
            $json = json_decode(file_get_contents($path), true);

            if (is_array($json)) {
                // filter only modules with "true"
                $enabled = array_filter($json, fn ($status) => $status === true);

                // return keys as both value & label
                return array_combine(array_keys($enabled), array_keys($enabled));
            }
        }

        return [];
    }

    // Scope by module + model
    public function scopeForModuleModel($query, string $module, string $model)
    {
        return $query
            ->where('module', strtolower($module))
            ->where('model', strtolower($model));
    }

    public function scopeVisibleInList($query)
    {
        return $query->where('show_in_list', true);
    }
}
