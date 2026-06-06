<?php

namespace Modules\Selling\Livewire\SalesOrders;

use Livewire\Component;
use Modules\Selling\Models\SalesOrder;
use Modules\Selling\Models\SalesOrderItem;
use Modules\Selling\Notifications\OrderUpdateNotification;

class Show extends Component
{
    public $orderId;
    public $order;

    public $showStatusModal = false;
    public $nextStatus = null;
    public $notifyCustomer = false;

    protected $listeners = ['refresh' => '$refresh'];

    public $isEmbedded = false;
    public $customerId = null;

    public function mount($id, $isEmbedded = false, $customerId = null)
    {
        $this->orderId = $id;
        $this->isEmbedded = $isEmbedded;
        $this->customerId = $customerId;
        $this->order = SalesOrder::with(['customer', 'items'])->findOrFail($id);
    }

    public function confirmUpdateStatus($status)
    {
        $this->nextStatus = $status;
        $this->notifyCustomer = false; // default to false
        $this->showStatusModal = true;
    }

    public function processStatusUpdate()
    {
        if (in_array($this->nextStatus, $this->order->getAllowedNextStatuses())) {
            $this->order->update(['status' => $this->nextStatus]);
            $this->order->refresh();

            if ($this->notifyCustomer && $this->order->customer) {
                $this->order->customer->notify(new OrderUpdateNotification($this->order));
            }

            session()->flash('message', "Order status updated to {$this->order->getStatusLabel()}.");
            $this->showStatusModal = false;
        } else {
            session()->flash('error', "Invalid status transition.");
        }
    }

    public function render()
    {
        return view('selling::livewire.sales-orders.show');
    }
}
?>
