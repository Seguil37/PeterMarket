@extends('layouts.app')
@section('title','Orden confirmada')

@section('content')
  <div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">¡Gracias por tu compra!</h1>

    <div class="bg-white rounded border p-4 mb-4">
      <p><strong>Orden #{{ $order->id }}</strong></p>
      <p>Estado: <span class="font-semibold">{{ strtoupper($order->status) }}</span></p>
      <p>Cliente: {{ $order->customer_name }} ({{ $order->customer_email }})</p>
      <p>Método de pago: {{ $order->payment_method }}</p>
      <p class="text-sm text-gray-500">Referencia: {{ $order->payment_ref }}</p>
    </div>

    <div class="bg-white rounded border p-4 mb-4">
      <h2 class="font-semibold mb-2">Datos de entrega</h2>
      <p><strong>Dirección:</strong> {{ $order->shipping_address }}</p>
      <p><strong>Ciudad / distrito:</strong> {{ $order->shipping_city }}</p>
      <p><strong>Referencia:</strong> {{ $order->shipping_reference }}</p>
      <p><strong>Tipo de envío:</strong> {{ $shippingOptions[$order->shipping_type]['label'] ?? $order->shipping_type }}</p>
      <p><strong>Costo de delivery:</strong> S/ {{ number_format($order->shipping_cost,2) }}</p>
    </div>

    <div class="bg-white rounded border p-4 mb-4">
      <h2 class="font-semibold mb-3">Productos</h2>
      <table class="w-full text-sm">
        <thead><tr class="border-b">
          <th class="text-left py-2">Producto</th>
          <th class="text-left">Categoría</th>
          <th>Cant.</th>
          <th>Precio</th>
          <th>Total</th>
        </tr></thead>
        <tbody>
          @foreach($order->items as $it)
          <tr class="border-b">
            <td class="py-2">{{ $it->name }}</td>
            <td class="py-2">{{ $it->category_type }}</td>
            <td class="text-center">{{ $it->quantity }}</td>
            <td class="text-center">S/ {{ number_format($it->price,2) }}</td>
            <td class="text-right">S/ {{ number_format($it->line_total,2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="bg-white rounded border p-4">
      <h2 class="font-semibold mb-3">Resumen de pago</h2>
      <div class="flex justify-between"><span>Subtotal productos:</span><strong>S/ {{ number_format($order->subtotal,2) }}</strong></div>
      <div class="flex justify-between"><span>Costo delivery:</span><strong>S/ {{ number_format($order->shipping_cost,2) }}</strong></div>
      <div class="flex justify-between text-lg mt-2 border-t pt-2"><span>Total final:</span><strong>S/ {{ number_format($order->total,2) }}</strong></div>
      <p class="text-xs text-gray-500 mt-1">Incluye IGV: S/ {{ number_format($order->tax,2) }}</p>

      <div class="mt-3 p-3 rounded bg-gray-50 border text-sm">
        <p class="font-semibold mb-1">{{ $deliveryEvaluation['message'] }}</p>
        @if($order->subtotal < \App\Support\Delivery::MIN_TOTAL)
          <p class="text-amber-700">El monto mínimo para delivery es S/ 35. Aumenta tu pedido o cambia a recojo en tienda.</p>
        @endif
      </div>

      <div class="mt-4 text-sm space-y-1 bg-emerald-50 border border-emerald-100 p-3 rounded">
        <p class="font-semibold">Información de referencia</p>
        <p>📍 Punto de origen del delivery: Jr. Espinar y Progreso – Cusco</p>
        <p>⏱ Tiempo estimado de entrega: 5 a 30 minutos según zona</p>
        <p>💬 Regla: Delivery gratis desde S/ 45</p>
      </div>

      <div class="mt-4">
        <p class="font-semibold mb-2 text-sm">Referencias por zona</p>
        <div class="overflow-x-auto">
          <table class="w-full text-sm border">
            <thead class="bg-gray-100">
              <tr>
                <th class="text-left p-2 border">Zona de envío</th>
                <th class="text-left p-2 border">Tiempo aprox</th>
                <th class="text-left p-2 border">Costo estimado para ti</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="p-2 border">Centro histórico</td>
                <td class="p-2 border">5–8 min</td>
                <td class="p-2 border">S/ 5–6</td>
              </tr>
              <tr class="bg-gray-50">
                <td class="p-2 border">Wanchaq / Av. La Cultura</td>
                <td class="p-2 border">10–15 min</td>
                <td class="p-2 border">S/ 6–8</td>
              </tr>
              <tr>
                <td class="p-2 border">Magisterio / Marcavalle</td>
                <td class="p-2 border">15–20 min</td>
                <td class="p-2 border">S/ 8–10</td>
              </tr>
              <tr class="bg-gray-50">
                <td class="p-2 border">San Sebastián / San Jerónimo</td>
                <td class="p-2 border">20–30 min</td>
                <td class="p-2 border">S/ 10–15</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <a href="{{ route('catalog.index') }}" class="inline-block mt-6 px-4 py-2 rounded bg-blue-600 text-white">Volver al catálogo</a>
  </div>
@endsection
