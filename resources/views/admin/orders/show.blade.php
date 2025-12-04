@extends('layouts.admin')
@section('admin-content')
<div class="flex flex-col gap-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">Pedido #{{ $order->id }}</p>
            <h2 class="text-2xl font-bold text-gray-900">{{ $order->customer_name }}</h2>
            <p class="text-gray-600">{{ $order->customer_email }}</p>
        </div>
        <div class="flex gap-4">
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-100">
                <p class="text-xs text-blue-600 uppercase font-semibold">Total</p>
                <p class="text-2xl font-bold text-blue-700">S/ {{ number_format($order->total, 2) }}</p>
            </div>
            <div class="p-4 rounded-xl bg-gray-50 border">
                <p class="text-xs text-gray-600 uppercase font-semibold">Estado</p>
                <p class="text-lg font-semibold">{{ \App\Support\OrderStatus::label($order->status) }}</p>
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-4">
            <h3 class="text-lg font-semibold">Productos</h3>
            <div class="overflow-hidden rounded-2xl border">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3">Producto</th>
                        <th class="px-4 py-3">Cant.</th>
                        <th class="px-4 py-3">Total línea</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">{{ $item->name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-sm font-semibold">S/ {{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="space-y-4">
            <h3 class="text-lg font-semibold">Datos de entrega</h3>
            <div class="rounded-xl border p-4 bg-gray-50 text-sm text-gray-700 space-y-2">
                <p><span class="font-semibold">Dirección:</span> {{ $order->shipping_address ?? 'No registrada' }}</p>
                <p><span class="font-semibold">Ciudad:</span> {{ $order->shipping_city ?? 'N/A' }}</p>
                <p><span class="font-semibold">Referencia:</span> {{ $order->shipping_reference ?? 'N/A' }}</p>
                <p><span class="font-semibold">Método de pago:</span> {{ $order->payment_method }}</p>
            </div>

            <h3 class="text-lg font-semibold">Actualizar estado</h3>
            <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="space-y-3">
                @csrf
                <label class="form-label">Selecciona nuevo estado</label>
                <select name="status" class="input" required>
                    @foreach($statusOptions as $key => $config)
                        <option value="{{ $key }}" @selected($order->status === $key)>{{ $config['label'] }}</option>
                    @endforeach
                </select>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="notify" value="1" class="h-4 w-4" checked>
                    Notificar al cliente por correo
                </label>
                <button type="submit" class="btn">Actualizar estado</button>
            </form>
        </div>
    </div>
</div>
@endsection
