<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Builder;

trait WithFilters
{
    public array $filters = [];

    protected function filterableConfig($model): array
    {
        $filterable = $model?->filterable ?? [];

        if (! is_array($filterable)) {
            return [];
        }

        if (function_exists('array_is_list') && array_is_list($filterable)) {
            $config = [];

            foreach ($filterable as $field) {
                if (! is_string($field) || $field === '') {
                    continue;
                }

                $config[$field] = $this->defaultFilterMeta($field);
            }

            return $config;
        }

        $config = [];

        foreach ($filterable as $field => $meta) {
            if (! is_string($field) || $field === '') {
                continue;
            }

            $config[$field] = is_array($meta) ? $meta : $this->defaultFilterMeta($field);
        }

        return $config;
    }

    protected function defaultFilterMeta(string $field): array
    {
        $relation = null;
        $column = $field;

        if (str_contains($field, '.')) {
            [$relation, $column] = explode('.', $field, 2);
        }

        $operator = match ($column) {
            'id',
            'company_id',
            'parent_id',
            'is_active',
            'is_default',
            'status',
            'type' => '=',
            default => 'like',
        };

        return [
            'type' => $operator === '=' ? 'select' : 'text',
            'operator' => $operator,
            'relation' => $relation,
            'column' => $column,
        ];
    }

    public function initFilters($model): void
    {
        foreach ($this->filterableConfig($model) as $field => $meta) {
            $this->filters[$field] = '';
        }
    }

    public function resetFilters(): void
    {
        foreach ($this->filters as $key => $val) {
            $this->filters[$key] = '';
        }
    }

    public function applyFilters(Builder $query, $model): Builder
    {
        foreach ($this->filterableConfig($model) as $field => $meta) {
            $value = $this->filters[$field] ?? null;
            if ($value === '' || $value === null) {
                continue;
            }

            $operator = $meta['operator'] ?? '=';
            $relation = $meta['relation'] ?? null;
            $column = $meta['column'] ?? $field;

            $apply = function (Builder $q) use ($column, $operator, $value): void {
                if ($operator === 'like') {
                    $q->where($column, 'like', "%{$value}%");

                    return;
                }
                $q->where($column, $operator, $value);
            };

            if ($relation) {
                $query->whereHas($relation, function (Builder $relQ) use ($apply): void {
                    $apply($relQ);
                });

                continue;
            }

            $apply($query);
        }

        return $query;
    }
}
