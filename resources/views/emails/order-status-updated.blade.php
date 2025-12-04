<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización de estado</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9fafb; color: #111827; }
        .container { max-width: 640px; margin: 0 auto; background: #ffffff; padding: 24px; border-radius: 8px; border: 1px solid #e5e7eb; }
        .header { margin-bottom: 16px; }
        .muted { color: #6b7280; font-size: 14px; }
        .badge { display: inline-block; padding: 6px 12px; border-radius: 9999px; background: #eef2ff; color: #3730a3; font-weight: 600; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Actualización de tu pedido #{{ $order->id }}</h1>
        @if($statusConfig)
            <span class="badge">{{ $statusConfig['label'] }}</span>
        @endif
        <p class="muted">Hola {{ $order->customer_name }}, tenemos novedades sobre tu compra.</p>
    </div>

    <p>{{ $statusConfig['mail_message'] ?? 'Tu pedido cambió de estado.' }}</p>

    <p class="muted" style="margin-top: 24px;">Si necesitas ayuda adicional, escríbenos respondiendo a este correo o llama al {{ \App\Support\OrderStatus::CONTACT_NUMBER }}.</p>
</div>
</body>
</html>
