<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPaidNotification extends Notification
{
    public function __construct(private Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $customerName = $this->order->user?->name ?? $this->order->guest_name ?? 'Guest';
        $total = 'Rp ' . number_format($this->order->total, 0, ',', '.');
        $adminUrl = rtrim(config('app.url'), '/') . '/admin/orders/' . $this->order->id;

        return (new MailMessage)
            ->subject('Pesanan Baru Dibayar — ' . $this->order->order_number)
            ->greeting('Halo Admin,')
            ->line('Pesanan baru telah berhasil dibayar.')
            ->line('**No. Pesanan:** ' . $this->order->order_number)
            ->line('**Customer:** ' . $customerName)
            ->line('**Total:** ' . $total)
            ->action('Lihat Pesanan', $adminUrl)
            ->salutation('BbystarGO Admin');
    }
}
