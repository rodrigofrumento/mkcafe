<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StoreReceiveNewOrder extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Novo pedido recebido')
            ->greeting('Olá, tudo bem?')
            ->line('Você possui um novo pedido na loja.')
            ->action('Ver pedido', route('admin.orders.my'));
    }

    public function toArray(object $notifiable): array
    {
        return ['message' => 'Você possui um novo pedido!'];
    }
}
