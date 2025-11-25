@extends('layouts.admin')
@section('admin-content')
<div class="space-y-8">

  <div class="flex flex-col gap-2">
    <p class="text-xs uppercase tracking-[0.25em] text-blue-600 font-semibold">Inventario</p>
    <div class="flex items-center justify-between gap-3 flex-wrap">
      <h1 class="text-3xl font-bold">Entradas y salidas</h1>
      <span class="inline-flex items-center gap-2 px-3 py-2 rounded-full text-xs font-semibold bg-amber-50 border border-amber-100 text-amber-700">
        🧾 {{ $movs->total() }} movimientos registrados
      </span>
    </div>
    <p class="text-sm text-gray-600">Registra movimientos con claridad, navega por el historial y mantén el stock bajo control.</p>
  </div>

  {{-- Formulario registrar movimiento --}}
  <div class="grid lg:grid-cols-3 gap-6">
    <form method="POST" action="{{ route('admin.inventory.store') }}" class="lg:col-span-2 card-surface p-6 space-y-6">
      @csrf
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs uppercase tracking-[0.18em] text-gray-500 font-semibold">Nuevo movimiento</p>
          <h2 class="text-xl font-semibold">Registrar entrada o salida</h2>
        </div>
        <span class="badge bg-blue-50 text-blue-700 border border-blue-100">Tiempo real</span>
      </div>

      <div class="grid md:grid-cols-2 gap-4">
        <div class="space-y-2">
          <label class="text-sm font-medium text-gray-700">Producto</label>
          <select name="product_id" class="w-full border border-gray-200 rounded-xl p-3 bg-white shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-200">
            @foreach($products as $p)
              <option value="{{ $p->id }}">{{ $p->name }} (stock: {{ $p->stock }})</option>
            @endforeach
          </select>
        </div>
        <div class="space-y-2">
          <label class="text-sm font-medium text-gray-700">Tipo</label>
          <div class="grid grid-cols-2 gap-3">
            <label class="flex items-center gap-2 p-3 rounded-xl border border-gray-200 bg-white shadow-inner cursor-pointer">
              <input type="radio" name="type" value="in" class="accent-blue-600" checked>
              <span class="text-sm font-semibold text-gray-700">Entrada</span>
            </label>
            <label class="flex items-center gap-2 p-3 rounded-xl border border-gray-200 bg-white shadow-inner cursor-pointer">
              <input type="radio" name="type" value="out" class="accent-blue-600">
              <span class="text-sm font-semibold text-gray-700">Salida</span>
            </label>
          </div>
        </div>
      </div>

      <div class="grid md:grid-cols-3 gap-4">
        <div class="space-y-2">
          <label class="text-sm font-medium text-gray-700">Cantidad</label>
          <input type="number" name="quantity" min="1" class="w-full border border-gray-200 rounded-xl p-3 shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-200" required>
        </div>
        <div class="space-y-2">
          <label class="text-sm font-medium text-gray-700">Costo unit. (opcional)</label>
          <input type="number" step="0.01" name="unit_cost" class="w-full border border-gray-200 rounded-xl p-3 shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-200">
        </div>
        <div class="space-y-2 md:col-span-3">
          <label class="text-sm font-medium text-gray-700">Nota</label>
          <input type="text" name="note" class="w-full border border-gray-200 rounded-xl p-3 shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-200" placeholder="Ej. Ajuste semanal, devolución, lote nuevo...">
        </div>
      </div>

      <div class="flex justify-end">
        <button class="btn-primary">Guardar movimiento</button>
      </div>
    </form>

    <div class="card-surface p-6 space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Búsqueda y filtros</h3>
        <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-100">Historial</span>
      </div>
      <p class="text-sm text-gray-600">Localiza movimientos por producto o nota y depura resultados rápidamente.</p>
      <form method="GET" class="space-y-3">
        <div class="relative">
          <input name="q" value="{{ $q }}" placeholder="Buscar por producto o nota" class="border border-gray-200 rounded-xl p-3 w-full bg-white shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-200">
          <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">⌕</span>
        </div>
        <div class="flex gap-3">
          <button class="btn-primary flex-1 justify-center">Buscar</button>
          <a href="{{ route('admin.inventory.index') }}" class="btn-outline">Limpiar</a>
        </div>
      </form>

      <div class="grid grid-cols-2 gap-3 text-sm">
        <div class="p-3 rounded-xl border border-gray-100 bg-emerald-50 text-emerald-700 font-semibold flex items-center gap-2">
          📈 <span>Stock en aumento si predominan entradas.</span>
        </div>
        <div class="p-3 rounded-xl border border-gray-100 bg-amber-50 text-amber-700 font-semibold flex items-center gap-2">
          ⚠️ <span>Revisa salidas repetidas para evitar quiebres.</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Tabla de movimientos --}}
  <div class="table-shell">
    <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-indigo-50 border-b border-gray-200 flex items-center justify-between flex-wrap gap-3">
      <div>
        <p class="text-xs uppercase tracking-[0.18em] text-gray-500 font-semibold">Historial</p>
        <h3 class="text-xl font-semibold text-gray-900">Movimientos recientes</h3>
      </div>
      <div class="flex items-center gap-2 text-sm text-gray-600">
        <span class="badge bg-emerald-100 text-emerald-700">Entradas</span>
        <span class="badge bg-rose-100 text-rose-700">Salidas</span>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-white">
          <tr class="text-left text-xs uppercase tracking-wide text-gray-500">
            <th class="p-4">Fecha</th>
            <th class="p-4">Producto</th>
            <th class="p-4 text-center">Tipo</th>
            <th class="p-4 text-center">Cantidad</th>
            <th class="p-4 text-center">Costo unit.</th>
            <th class="p-4">Nota</th>
            <th class="p-4 text-right">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($movs as $m)
          <tr class="hover:bg-indigo-50/60">
            <td class="p-4 text-gray-700">{{ $m->created_at->format('Y-m-d H:i') }}</td>
            <td class="p-4 font-semibold text-gray-900">{{ $m->product->name }}</td>
            <td class="p-4 text-center">
              <span class="badge {{ $m->type === 'in' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                {{ $m->type === 'in' ? 'Entrada' : 'Salida' }}
              </span>
            </td>
            <td class="p-4 text-center font-semibold text-gray-900">{{ $m->quantity }}</td>
            <td class="p-4 text-center text-gray-700">{{ $m->unit_cost ? number_format($m->unit_cost,2) : '—' }}</td>
            <td class="p-4 text-gray-700">{{ $m->note }}</td>
            <td class="p-4 text-right">
              <form method="POST" action="{{ route('admin.inventory.destroy',$m) }}" onsubmit="return confirm('Eliminar movimiento y revertir stock?')">
                @csrf @method('DELETE')
                <button class="btn-outline text-sm text-rose-700 border-rose-200 hover:border-rose-300">Eliminar</button>
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="p-6 text-center text-gray-500">No hay movimientos que coincidan con tu búsqueda.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-4 bg-white border-t border-gray-100">{{ $movs->links() }}</div>
  </div>
</div>
@endsection
