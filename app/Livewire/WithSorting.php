<?php

namespace App\Livewire;

trait WithSorting
{
    public $sortBy = 'id';

    public $sortDirection = 'desc';

    public $orderable = [];

    public function sort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->reverseSort();
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function reverseSort()
    {
        return $this->sortDirection === 'asc'
            ? 'desc'
            : 'asc';
    }
}
