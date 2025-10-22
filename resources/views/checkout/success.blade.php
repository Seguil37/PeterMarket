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
      <h2 class="font-semibold mb-3">Productos</h2>
      <table class="w-full text-sm">
        <thead><tr class="border-b">
          <th class="text-left py-2">Producto</th>
          <th>Cant.</th>
          <th>Precio</th>
          <th>Total</th>
        </tr></thead>
        <tbody>
          @foreach($order->items as $it)
          <tr class="border-b">
            <td class="py-2">{{ $it->name }}</td>
            <td class="text-center">{{ $it->quantity }}</td>
            <td class="text-center">S/ {{ number_format($it->price,2) }}</td>
            <td class="text-right">S/ {{ number_format($it->line_total,2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="bg-white rounded border p-4">
      <div class="flex justify-between"><span>Subtotal</span><strong>S/ {{ number_format($order->subtotal,2) }}</strong></div>
      <div class="flex justify-between"><span>IGV</span><strong>S/ {{ number_format($order->tax,2) }}</strong></div>
      <div class="flex justify-between text-lg mt-2 border-t pt-2"><span>Total</span><strong>S/ {{ number_format($order->total,2) }}</strong></div>
    </div>

    <a href="{{ route('catalog.index') }}" class="inline-block mt-6 px-4 py-2 rounded bg-blue-600 text-white">Volver al catálogo</a>
  </div>
@endsection
