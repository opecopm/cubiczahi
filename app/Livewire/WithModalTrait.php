<?php

namespace App\Livewire;

trait WithModalTrait
{
    public $showModal = false;

    public $deleteId;

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        if (@$this->deleteId) {
            $this->deleteId = '';
        }
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    public function cancelDelete()
    {
        $this->deleteId = null;
    }
}
