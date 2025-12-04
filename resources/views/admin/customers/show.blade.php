@extends('layouts.admin')
@section('admin-content')
<div class="flex flex-col gap-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">Cliente #{{ $customer->id }}</p>
            <h2 class="text-2xl font-bold text-gray-900">{{ $customer->name }}</h2>
            <p class="text-gray-600">{{ $customer->email }}</p>
        </div>
        <div class="flex gap-4">
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-100">
                <p class="text-xs text-blue-600 uppercase font-semibold">Pedidos</p>
                <p class="text-2xl font-bold text-blue-700">{{ $customer->orders()->count() }}</p>
            </div>
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100">
                <p class="text-xs text-emerald-600 uppercase font-semibold">Total</p>
                <p class="text-2xl font-bold text-emerald-700">S/ {{ number_format($customer->orders()->sum('total'), 2) }}</p>
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900">Actualizar perfil</h3>
            <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="form-label">Nombre completo</label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" class="input" required>
                </div>
                <div>
                    <label class="form-label">Correo</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="input" required>
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="is_active" value="1" class="h-4 w-4" {{ old('is_active', $customer->is_active) ? 'checked' : '' }}>
                    Cuenta activa
                </label>
                <button type="submit" class="btn">Guardar cambios</button>
            </form>
        </div>

        <div class="space-y-3">
            <h3 class="text-lg font-semibold text-gray-900">Detalle</h3>
            <div class="rounded-xl border p-4 bg-gray-50 text-sm text-gray-700 space-y-2">
                <p><span class="font-semibold">Registrado:</span> {{ optional($customer->created_at)->format('d/m/Y H:i') }}</p>
                <p><span class="font-semibold">Último pedido:</span> {{ optional($customer->orders()->latest()->first()?->created_at)->format('d/m/Y H:i') ?? 'Sin pedidos' }}</p>
                <p><span class="font-semibold">Estado:</span> {{ $customer->is_active ? 'Activo' : 'Inactivo' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold">Historial de pedidos</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:underline">Ir a panel de pedidos</a>
        </div>
        <div class="overflow-hidden rounded-2xl border">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">
                <tr>
                    <th class="px-4 py-3">Pedido</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3">Fecha</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @foreach($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-700 hover:underline">#{{ $order->id }}</a>
                        </td>
                        <td class="px-4 py-3 text-sm">S/ {{ number_format($order->total, 2) }}</td>
                        <td class="px-4 py-3 text-xs font-semibold">
                            <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700">{{ \App\Support\OrderStatus::label($order->status) }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $orders->links() }}
    </div>
</div>
@endsection
