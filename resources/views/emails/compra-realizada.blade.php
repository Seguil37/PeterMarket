<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de compra</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9fafb; color: #111827; }
        .container { max-width: 640px; margin: 0 auto; background: #ffffff; padding: 24px; border-radius: 8px; border: 1px solid #e5e7eb; }
        .header { margin-bottom: 16px; }
        .items { width: 100%; border-collapse: collapse; margin-top: 12px; }
        .items th, .items td { padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: left; font-size: 14px; }
        .items th { background: #f3f4f6; }
        .total { text-align: right; font-size: 16px; font-weight: bold; margin-top: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>¡Gracias por tu compra, {{ $order->customer_name }}!</h1>
        <p>Tu pedido #{{ $order->id }} ha sido confirmado.</p>
    </div>

    <p><strong>Método de entrega:</strong>
        @if($order->delivery_type === 'pickup')
            Recoger en tienda
        @else
            Delivery a domicilio
        @endif
    </p>
    @if($order->delivery_type === 'delivery')
        <p><strong>Dirección de envío:</strong> {{ $order->shipping_address }}</p>
        <p><strong>Ciudad / distrito:</strong> {{ $order->shipping_city }}</p>
        <p><strong>Referencia:</strong> {{ $order->shipping_reference }}</p>
    @else
        <p>Recogerás tu pedido en tienda. ¡Te avisaremos cuando esté listo!</p>
    @endif

    <h3>Productos</h3>
    <table class="items">
        <thead>
        <tr>
            <th>Producto</th>
            <th>Cant.</th>
            <th>Subtotal</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->items as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>S/ {{ number_format($item->line_total, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <p class="total">Total pagado: S/ {{ number_format($order->total, 2) }}</p>

    <p style="font-size: 12px; color: #6b7280; margin-top: 16px;">Si tienes dudas sobre tu pedido, responde a este correo y te ayudaremos.</p>
</div>
</body>
</html>
