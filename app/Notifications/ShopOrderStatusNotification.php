<?php

namespace App\Notifications;

use App\Models\ShopOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShopOrderStatusNotification extends Notification
{
    use Queueable;

    public function __construct(public ShopOrder $order, public string $previousStatus) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $statusLabel = ucfirst($this->order->status);

        return (new MailMessage)
            ->subject("Your Order #{$this->order->order_number} is {$statusLabel}")
            ->view('emails.shop-order-status', [
                'order'          => $this->order,
                'previousStatus' => $this->previousStatus,
            ]);
    }
}
