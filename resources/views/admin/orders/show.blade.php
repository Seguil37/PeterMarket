@extends('layouts.admin')
@section('admin-content')
<div class="flex flex-col gap-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">Pedido #{{ $order->id }}</p>
            <h2 class="text-2xl font-bold text-gray-900">{{ $order->customer_name }}</h2>
            <p class="text-gray-600">{{ $order->customer_email }}</p>
        </div>
        <div class="flex gap-4 items-center flex-wrap justify-end">
            <a href="{{ route('admin.orders.pdf', $order) }}" class="btn-secondary">Descargar PDF</a>
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
            <form id="order-status-form" method="POST" action="{{ route('admin.orders.status', $order) }}" class="space-y-3">
                @csrf
                <label class="form-label">Selecciona nuevo estado</label>
                <select id="order-status-select" name="status" class="input" required>
                    @foreach($statusOptions as $key => $config)
                        <option value="{{ $key }}" @selected($order->status === $key)>{{ $config['label'] }}</option>
                    @endforeach
                </select>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="notify" value="1" class="h-4 w-4" checked>
                    Notificar al cliente por correo
                </label>
                <p class="text-xs text-gray-500">Estados disponibles: salió de la tienda, en camino y llegó al destino. Si el cliente no recibió su pedido podrá responder usando el número {{ \App\Support\OrderStatus::CONTACT_NUMBER }}.</p>
                <button type="button" id="open-status-modal" class="flex items-center justify-center gap-2 w-full rounded-lg bg-blue-600 text-white font-semibold py-3 shadow-lg hover:bg-blue-700 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.5m0 3h.01m-.01-12a9 9 0 100 18 9 9 0 000-18z" />
                    </svg>
                    Confirmar estado
                </button>
            </form>
        </div>
    </div>

    <div id="status-modal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-30">
        <div class="min-h-full flex items-center justify-center px-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 space-y-4">
                <div class="flex items-start gap-3">
                    <div class="h-10 w-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xl">⚠️</div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900">¿Está seguro de cambiar el estado del pedido?</h4>
                        <p id="status-modal-text" class="text-sm text-gray-700 mt-1">El nuevo estado seleccionado es: ""</p>
                        <p class="text-xs text-gray-500 mt-2">Esta acción enviará una notificación por correo al cliente.</p>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" id="cancel-status" class="btn-secondary">Cancelar</button>
                    <button type="button" id="confirm-status" class="btn">Sí, confirmar cambio</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('status-modal');
        const openButton = document.getElementById('open-status-modal');
        const cancelButton = document.getElementById('cancel-status');
        const confirmButton = document.getElementById('confirm-status');
        const statusSelect = document.getElementById('order-status-select');
        const statusText = document.getElementById('status-modal-text');
        const form = document.getElementById('order-status-form');

        function toggleModal(show) {
            modal.classList.toggle('hidden', !show);
        }

        function updateStatusLabel() {
            const option = statusSelect.options[statusSelect.selectedIndex];
            statusText.textContent = `El nuevo estado seleccionado es: "${option.textContent}"`;
        }

        openButton?.addEventListener('click', (event) => {
            event.preventDefault();
            updateStatusLabel();
            toggleModal(true);
        });

        cancelButton?.addEventListener('click', () => toggleModal(false));
        confirmButton?.addEventListener('click', () => form.submit());
    });
</script>
@endpush
@endsection
