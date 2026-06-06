<?php

namespace Modules\Selling\Livewire\SalesInvoices;

use Livewire\Component;
use Modules\Selling\Models\SalesInvoice;
use Modules\Selling\Models\SalesInvoiceItem;

class Show extends Component
{
    public $invoiceId;
    public $invoice;

    public $showStatusModal = false;
    public $nextStatus = null;

    protected $listeners = ['refresh' => '$refresh'];

    public $isEmbedded = false;
    public $customerId = null;

    public function mount($salesInvoiceId, $isEmbedded = false, $customerId = null)
    {
        $this->invoiceId = $salesInvoiceId;
        $this->isEmbedded = $isEmbedded;
        $this->customerId = $customerId;
        $this->invoice = SalesInvoice::with(['customer', 'items'])->findOrFail($salesInvoiceId);
    }

    public function confirmUpdateStatus($status)
    {
        $this->nextStatus = $status;
        $this->showStatusModal = true;
    }

    public function processStatusUpdate()
    {
        if (array_key_exists($this->nextStatus, SalesInvoice::STATUS_SELECT)) {
            $this->invoice->update(['status' => $this->nextStatus]);
            $this->invoice->refresh();

            session()->flash('message', "Invoice status updated to " . SalesInvoice::STATUS_SELECT[$this->nextStatus] . ".");
            $this->showStatusModal = false;
        } else {
            session()->flash('error', "Invalid status transition.");
        }
    }

    public function render()
    {
        return view('selling::livewire.sales-invoices.show');
    }
}
