@extends('layouts.admin')
@section('admin-content')
<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
  <form method="GET" class="flex gap-2 items-center">
    <input name="s" value="{{ request('s') }}" placeholder="Buscar productos" class="border border-slate-200 rounded-lg px-3 py-2 shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-200">
    <button class="btn-outline">Filtrar</button>
  </form>
  <a href="{{ route('admin.products.create') }}" class="btn-primary">Nuevo producto</a>
</div>

<div class="table-shell">
  <div class="px-5 py-4 flex items-center justify-between bg-white border-b border-slate-100">
    <div>
      <p class="text-xs uppercase tracking-[0.18em] text-gray-500 font-semibold">Catálogo</p>
      <h2 class="text-lg font-semibold text-gray-900">Productos publicados</h2>
    </div>
    <span class="badge bg-blue-50 text-blue-700 border border-blue-100">{{ $products->total() }} items</span>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-gray-600 uppercase text-xs tracking-wide">
        <tr>
          <th class="p-3 text-left">ID</th>
          <th class="p-3 text-left">Nombre</th>
          <th class="p-3 text-left">Categoría</th>
          <th class="p-3 text-left">Precio</th>
          <th class="p-3 text-left">Stock</th>
          <th class="p-3 text-left">Imagen</th>
          <th class="p-3 text-right">Acciones</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($products as $p)
        <tr class="hover:bg-blue-50/60">
          <td class="p-3 font-semibold text-gray-800">{{ $p->id }}</td>
          <td class="p-3 text-gray-800">{{ $p->name }}</td>
          <td class="p-3 text-gray-600">{{ $p->category_type }}</td>
          <td class="p-3 font-semibold text-gray-900">S/ {{ number_format($p->price,2) }}</td>
          <td class="p-3 text-gray-800">{{ $p->stock }}</td>
          <td class="p-3">
            @if($p->image_url)
              <img src="{{ $p->image_url }}" class="h-12 w-12 object-cover rounded-xl border border-slate-100">
            @endif
          </td>
          <td class="p-3 text-right space-x-1">
            <a href="{{ route('admin.products.edit',$p) }}" class="btn-ghost">Editar</a>
            <form action="{{ route('admin.products.destroy',$p) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar producto?')">
              @csrf @method('DELETE')
              <button class="btn-ghost text-red-600 hover:text-red-700">Eliminar</button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="p-8 text-center">
            <div class="space-y-2 text-gray-600">
              <div class="text-3xl">📦</div>
              <p class="font-semibold text-gray-900">Aún no hay productos registrados.</p>
              <p class="text-sm">Publica tu primer artículo para verlo en el catálogo.</p>
              <a href="{{ route('admin.products.create') }}" class="btn-primary justify-center">Agregar producto</a>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="px-4 py-3 bg-white border-t border-slate-100">{{ $products->links() }}</div>
</div>
@endsection
