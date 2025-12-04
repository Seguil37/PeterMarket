@extends('layouts.admin')
@section('admin-content')
<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Clientes</h2>
            <p class="text-gray-600 text-sm">Gestiona usuarios y revisa sus compras.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <form method="GET" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ $search }}" placeholder="Buscar por nombre o correo" class="input" />
                <button type="submit" class="btn">Buscar</button>
            </form>
            <a href="{{ route('admin.reports.monthly') }}" class="btn-secondary">Ver reporte mensual</a>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">
            <tr>
                <th class="px-4 py-3">Cliente</th>
                <th class="px-4 py-3">Correo</th>
                <th class="px-4 py-3">Pedidos</th>
                <th class="px-4 py-3">Total comprado</th>
                <th class="px-4 py-3">Acciones</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($customers as $customer)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-semibold">{{ $customer->name }}</div>
                        <div class="text-xs text-gray-500">ID: {{ $customer->id }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $customer->email }}</td>
                    <td class="px-4 py-3 text-sm">{{ $customer->orders_count }}</td>
                    <td class="px-4 py-3 text-sm font-semibold">S/ {{ number_format($customer->total_spent ?? 0, 2) }}</td>
                    <td class="px-4 py-3 text-sm">
                        <a href="{{ route('admin.customers.show', $customer) }}" class="text-blue-600 hover:underline">Ver detalle</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">No se encontraron clientes.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $customers->links() }}
</div>
@endsection
