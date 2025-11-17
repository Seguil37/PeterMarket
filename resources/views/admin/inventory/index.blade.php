@extends('layouts.admin')
@section('admin-content')
<div class="space-y-8">

  <h1 class="text-2xl font-semibold">Inventario (entradas/salidas)</h1>

  {{-- Formulario registrar movimiento --}}
  <form method="POST" action="{{ route('admin.inventory.store') }}" class="grid md:grid-cols-5 gap-3 items-end bg-white p-4 rounded-xl shadow">
    @csrf
    <div>
      <label class="text-sm">Producto</label>
      <select name="product_id" class="w-full border rounded p-2">
        @foreach($products as $p)
          <option value="{{ $p->id }}">{{ $p->name }} (stock: {{ $p->stock }})</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="text-sm">Tipo</label>
      <select name="type" class="w-full border rounded p-2">
        <option value="in">Entrada</option>
        <option value="out">Salida</option>
      </select>
    </div>
    <div>
      <label class="text-sm">Cantidad</label>
      <input type="number" name="quantity" min="1" class="w-full border rounded p-2" required>
    </div>
    <div>
      <label class="text-sm">Costo unit. (opcional)</label>
      <input type="number" step="0.01" name="unit_cost" class="w-full border rounded p-2">
    </div>
    <div class="md:col-span-5">
      <label class="text-sm">Nota</label>
      <input type="text" name="note" class="w-full border rounded p-2">
    </div>
    <div class="md:col-span-5">
      <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">Guardar</button>
    </div>
  </form>

  {{-- Buscador --}}
  <form method="GET" class="flex gap-2">
    <input name="q" value="{{ $q }}" placeholder="Buscar por producto o nota" class="border rounded p-2 w-full">
    <button class="px-3 py-2 border rounded">Buscar</button>
  </form>

  {{-- Tabla de movimientos --}}
  <div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="p-3 text-left">Fecha</th>
          <th class="p-3 text-left">Producto</th>
          <th class="p-3">Tipo</th>
          <th class="p-3">Cantidad</th>
          <th class="p-3">Costo unit.</th>
          <th class="p-3 text-left">Nota</th>
          <th class="p-3"></th>
        </tr>
      </thead>
      <tbody>
        @foreach($movs as $m)
        <tr class="border-t">
          <td class="p-3">{{ $m->created_at->format('Y-m-d H:i') }}</td>
          <td class="p-3">{{ $m->product->name }}</td>
          <td class="p-3">{{ $m->type === 'in' ? 'Entrada' : 'Salida' }}</td>
          <td class="p-3 text-center">{{ $m->quantity }}</td>
          <td class="p-3 text-center">{{ $m->unit_cost ? number_format($m->unit_cost,2) : '—' }}</td>
          <td class="p-3">{{ $m->note }}</td>
          <td class="p-3 text-right">
            <form method="POST" action="{{ route('admin.inventory.destroy',$m) }}" onsubmit="return confirm('Eliminar movimiento y revertir stock?')">
              @csrf @method('DELETE')
              <button class="text-red-600 hover:underline">Eliminar</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="p-3">{{ $movs->links() }}</div>
  </div>
</div>
@endsection
