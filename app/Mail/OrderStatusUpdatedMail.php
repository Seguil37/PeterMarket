<?php

namespace App\Mail;

use App\Models\Order;
use App\Support\OrderStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $status;

    public function __construct(Order $order, string $status)
    {
        $this->order = $order;
        $this->status = $status;
    }

    public function build(): self
    {
        $statusConfig = OrderStatus::options()[$this->status] ?? null;

        return $this
            ->subject(($statusConfig['mail_subject'] ?? 'Actualización de pedido') . ' #' . $this->order->id)
            ->view('emails.order-status-updated', [
                'order' => $this->order,
                'status' => $this->status,
                'statusConfig' => $statusConfig,
            ]);
    }
}
