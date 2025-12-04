@extends('layouts.admin')
@section('admin-content')
<div class="flex flex-col gap-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Reporte mensual</h2>
            <p class="text-sm text-gray-600">Ventas, clientes frecuentes y productos destacados por mes.</p>
        </div>
        <form method="GET" class="flex items-center gap-2 flex-wrap">
            <input type="month" name="month" value="{{ $month }}" class="input" />
            <button class="btn" type="submit">Actualizar</button>
            <a class="btn-secondary" href="{{ route('admin.reports.monthly.pdf', ['month' => $month]) }}">Descargar PDF</a>
        </form>
    </div>

    <div class="grid md:grid-cols-4 gap-4">
        <div class="rounded-2xl border p-4 bg-white">
            <p class="text-xs text-gray-500">Total de ventas</p>
            <p class="text-2xl font-bold">S/ {{ number_format($totalSales, 2) }}</p>
        </div>
        <div class="rounded-2xl border p-4 bg-white">
            <p class="text-xs text-gray-500">Pedidos emitidos</p>
            <p class="text-2xl font-bold">{{ $ordersCount }}</p>
        </div>
        <div class="rounded-2xl border p-4 bg-white">
            <p class="text-xs text-gray-500">Clientes con compras</p>
            <p class="text-2xl font-bold">{{ $frequentCustomers->where('orders_count', '>', 0)->count() }}</p>
        </div>
        <div class="rounded-2xl border p-4 bg-white">
            <p class="text-xs text-gray-500">Total recaudado</p>
            <p class="text-2xl font-bold">S/ {{ number_format($totalSales, 2) }}</p>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="rounded-2xl border p-4 bg-white space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Clientes frecuentes</h3>
                <a href="{{ route('admin.customers.index') }}" class="text-blue-600 hover:underline text-sm">Ir a clientes</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($frequentCustomers as $customer)
                    <div class="py-3 flex items-start justify-between gap-3">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $customer->name }}</div>
                            <div class="text-xs text-gray-600">{{ $customer->email }}</div>
                        </div>
                        <div class="text-right text-sm">
                            <p class="font-semibold text-blue-700">{{ $customer->orders_count }} pedidos</p>
                            <p class="text-xs text-gray-500">S/ {{ number_format($customer->total_spent ?? 0, 2) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-3">Aún no hay clientes con compras este mes.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border p-4 bg-white space-y-3">
            <h3 class="text-lg font-semibold">Productos más vendidos</h3>
            <div class="overflow-hidden rounded-xl border">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">
                        <tr>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3">Unidades</th>
                            <th class="px-4 py-3">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($topProducts as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm">{{ $product->name ?? 'Producto eliminado' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $product->total_quantity }}</td>
                                <td class="px-4 py-3 text-sm font-semibold">S/ {{ number_format($product->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-gray-500">No hay productos vendidos en este mes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border p-4 bg-white space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold">Pedidos del mes</h3>
            <p class="text-xs text-gray-500">Se enviarán notificaciones con el número {{ $contactNumber }} cuando corresponda.</p>
        </div>
        <div class="overflow-hidden rounded-xl border">
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
                @forelse($orders as $order)
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
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">No hay pedidos registrados en el mes seleccionado.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $orders->links() }}
    </div>
</div>
@endsection
