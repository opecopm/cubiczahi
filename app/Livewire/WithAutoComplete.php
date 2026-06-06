<?php

namespace App\Livewire;

trait WithAutoComplete
{
    public function getSuggestions($modelClass, $searchTerm, $columns)
    {
        $model = new $modelClass;
        $query = $model::query();

        if (strlen($searchTerm) <= 1) {
            return [];
        }

        foreach ($columns as $column) {
            $query->orWhere($column, 'LIKE', '%'.$searchTerm.'%');
        }

        return $query->get();
    }
}
