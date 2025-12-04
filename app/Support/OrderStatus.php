<?php

namespace App\Support;

class OrderStatus
{
    public const CONTACT_NUMBER = '+51 949 758 387';

    public static function options(): array
    {
        return [
            'paid' => [
                'label'        => 'Pagado',
                'mail_subject' => 'Pago confirmado',
                'mail_message' => 'Hemos confirmado tu pago y estamos preparando tu pedido.',
            ],
            'shipped' => [
                'label'        => 'Salió de la tienda',
                'mail_subject' => 'Tu pedido salió de la tienda',
                'mail_message' => 'El pedido ha salido de la tienda.',
            ],
            'on_route' => [
                'label'        => 'En camino',
                'mail_subject' => 'Tu pedido está en camino',
                'mail_message' => 'El pedido está en camino.',
            ],
            'delivered' => [
                'label'        => 'Entregado',
                'mail_subject' => 'Pedido entregado',
                'mail_message' => 'El pedido ha llegado al destino.',
            ],
            'incident' => [
                'label'        => 'Incidencia',
                'mail_subject' => 'Ayuda con tu entrega',
                'mail_message' => 'No hemos podido confirmar la entrega. Si no has recibido tu pedido, comunícate al ' . self::CONTACT_NUMBER . '.',
            ],
        ];
    }

    public static function label(string $status): string
    {
        return static::options()[$status]['label'] ?? ucfirst($status);
    }
}
