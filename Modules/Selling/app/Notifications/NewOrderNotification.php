<?php

namespace Modules\Selling\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Selling\Models\SalesOrder;

class NewOrderNotification extends Notification
{
    use Queueable;

    public function __construct(public SalesOrder $order) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->order;

        return (new MailMessage)
            ->subject('New Order Received: ' . $order->reference)
            ->greeting('Hello ' . ($notifiable->first_name ?? $notifiable->name ?? 'Team') . ',')
            ->line('A new laundry order has been placed and requires your attention.')
            ->line('')
            ->line('**Order:** ' . $order->reference)
            ->line('**Customer:** ' . ($order->customer->name ?? 'N/A'))
            ->line('**Total:** ' . number_format($order->total, 2) . ' ' . $order->currency)
            ->line('**Delivery:** ' . ($order->deliveryMethod->name ?? 'Not specified'))
            ->line('**Date:** ' . $order->order_date)
            ->action('View Order', route('admin.selling.sales-orders.show', $order->id))
            ->line('Please process this order as soon as possible.')
            ->salutation('— ' . config('app.name'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'New order ' . $this->order->reference . ' placed by ' . ($this->order->customer->name ?? 'a customer'),
            'url'     => route('admin.selling.sales-orders.show', $this->order->id),
            'order_id'    => $this->order->id,
            'order_ref'   => $this->order->reference,
            'customer'    => $this->order->customer->name ?? null,
            'total'       => $this->order->total,
        ];
    }
}
