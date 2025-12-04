@extends('layouts.admin')
@section('admin-content')
<div class="flex flex-col gap-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Panel de pedidos</h2>
            <p class="text-sm text-gray-600">Revisa ventas por mes, genera reportes y accede al historial.</p>
        </div>
        <div class="flex gap-2">
            <form method="GET" class="flex items-center gap-2">
                <select name="status" class="input">
                    <option value="">Todos los estados</option>
                    @foreach($statusOptions as $key => $config)
                        <option value="{{ $key }}" @selected($status === $key)>{{ $config['label'] }}</option>
                    @endforeach
                </select>
                <input type="month" name="month" value="{{ $month }}" class="input" />
                <button class="btn">Filtrar</button>
            </form>
            <a class="btn-secondary" href="{{ route('admin.orders.report', ['month' => $month]) }}">Descargar PDF</a>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-4">
        @foreach($monthlySales as $row)
            <div class="rounded-xl border p-4 bg-white">
                <p class="text-xs text-gray-500">{{ $row->period }}</p>
                <p class="text-2xl font-bold">S/ {{ number_format($row->total_sales, 2) }}</p>
                <p class="text-sm text-gray-600">{{ $row->orders_count }} pedidos</p>
            </div>
        @endforeach
    </div>

    <div class="rounded-2xl border p-4 bg-white">
        <h3 class="text-lg font-semibold mb-4">Clientes frecuentes</h3>
        <div class="grid md:grid-cols-5 gap-3">
            @foreach($topCustomers as $customer)
                <div class="rounded-lg border p-3 bg-gray-50">
                    <div class="font-semibold text-gray-900">{{ $customer->name }}</div>
                    <div class="text-xs text-gray-600">{{ $customer->email }}</div>
                    <div class="text-sm font-semibold text-blue-700 mt-1">{{ $customer->orders_count }} pedidos</div>
                    <div class="text-xs text-gray-500">S/ {{ number_format($customer->total_spent, 2) }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">
            <tr>
                <th class="px-4 py-3">Pedido</th>
                <th class="px-4 py-3">Cliente</th>
                <th class="px-4 py-3">Total</th>
                <th class="px-4 py-3">Estado</th>
                <th class="px-4 py-3">Fecha</th>
                <th class="px-4 py-3">Acciones</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @foreach($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-semibold">#{{ $order->id }}</td>
                    <td class="px-4 py-3 text-sm">
                        <div class="font-medium">{{ $order->customer_name }}</div>
                        <div class="text-xs text-gray-500">{{ $order->customer_email }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold">S/ {{ number_format($order->total, 2) }}</td>
                    <td class="px-4 py-3 text-xs font-semibold">
                        <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700">{{ \App\Support\OrderStatus::label($order->status) }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 text-sm">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:underline">Ver detalle</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $orders->links() }}
</div>
@endsection
