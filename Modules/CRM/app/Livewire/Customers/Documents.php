<?php

namespace Modules\CRM\Livewire\Customers;

use App\Livewire\WithModalTrait;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\CRM\Models\Customer;
use Modules\Global\Models\Country;
use Modules\Global\Models\GeneralDocumentType;

class Documents extends Component
{
    use WithFileUploads, WithModalTrait;

    public $customer;

    public $documentId;

    // Form fields
    public $name;

    public $type;

    public $document_number;

    public $issue_date;

    public $expiry_date;

    public $issuing_country;

    public $issuing_entity;

    public $description;

    public $file;

    public $status = 'active';

    public $countries = [];

    public $documentTypes = [];

    public function mount(Customer $customer)
    {

        $this->customer = $customer;
        $this->countries = Country::orderBy('name')->get();
        $this->documentTypes = GeneralDocumentType::active()->orderBy('name')->get();
    }

    public function render()
    {
        $documents = $this->customer->generalDocuments()->latest()->get();

        return view('crm::livewire.customers.documents', compact('documents'));
    }

    public function create()
    {
        $this->resetForm();
        $this->openModal();
    }

    public function edit($id)
    {
        $this->resetForm();
        $document = $this->customer->generalDocuments()->findOrFail($id);
        $this->documentId = $id;

        $this->name = $document->name;
        $this->type = $document->type;
        $this->document_number = $document->document_number;
        $this->issue_date = $document->issue_date;
        $this->expiry_date = $document->expiry_date;
        $this->issuing_country = $document->issuing_country;
        $this->issuing_entity = $document->issuing_entity;
        $this->description = $document->description;
        $this->status = $document->status;

        $this->openModal();
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'document_number' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $document = $this->customer->generalDocuments()->create([
            'name' => $this->name,
            'type' => $this->type,
            'document_number' => $this->document_number,
            'issue_date' => $this->issue_date,
            'expiry_date' => $this->expiry_date,
            'issuing_country' => $this->issuing_country,
            'issuing_entity' => $this->issuing_entity,
            'description' => $this->description,
            'status' => $this->status,
        ]);

        if ($this->file) {
            $document->addMedia($this->file)->toMediaCollection('documents');
        }

        $this->closeModal();
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Document added successfully.']);
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'document_number' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'file' => 'nullable|file|max:10240',
        ]);

        $document = $this->customer->generalDocuments()->findOrFail($this->documentId);

        $document->update([
            'name' => $this->name,
            'type' => $this->type,
            'document_number' => $this->document_number,
            'issue_date' => $this->issue_date,
            'expiry_date' => $this->expiry_date,
            'issuing_country' => $this->issuing_country,
            'issuing_entity' => $this->issuing_entity,
            'description' => $this->description,
            'status' => $this->status,
        ]);

        if ($this->file) {
            $document->clearMediaCollection('documents');
            $document->addMedia($this->file)->toMediaCollection('documents');
        }

        $this->closeModal();
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Document updated successfully.']);
    }

    public function delete($id)
    {
        $document = $this->customer->generalDocuments()->findOrFail($id);
        $document->delete();
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Document deleted successfully.']);
    }

    public function resetForm()
    {
        $this->reset([
            'documentId', 'name', 'type', 'document_number', 'issue_date',
            'expiry_date', 'issuing_country', 'issuing_entity', 'description',
            'file', 'status',
        ]);
        $this->status = 'active';
    }
}
