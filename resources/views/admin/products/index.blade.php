@extends('layouts.admin')
@section('admin-content')
<div class="flex items-center justify-between mb-4">
  <form method="GET" class="flex gap-2">
    <input name="s" value="{{ request('s') }}" placeholder="Buscar..." class="border rounded px-3 py-2">
    <button class="border rounded px-3 py-2">Filtrar</button>
  </form>
  <a href="{{ route('admin.products.create') }}" class="bg-black text-white px-4 py-2 rounded">Nuevo</a>
</div>

<div class="overflow-x-auto">
  <table class="min-w-full border">
    <thead>
      <tr class="bg-gray-50">
        <th class="p-2 border">ID</th>
        <th class="p-2 border">Nombre</th>
        <th class="p-2 border">Categoría</th>
        <th class="p-2 border">Precio</th>
        <th class="p-2 border">Stock</th>
        <th class="p-2 border">Img</th>
        <th class="p-2 border"></th>
      </tr>
    </thead>
    <tbody>
      @foreach($products as $p)
      <tr>
        <td class="p-2 border">{{ $p->id }}</td>
        <td class="p-2 border">{{ $p->name }}</td>
        <td class="p-2 border">{{ $p->category_type }}</td>
        <td class="p-2 border">S/ {{ number_format($p->price,2) }}</td>
        <td class="p-2 border">{{ $p->stock }}</td>
        <td class="p-2 border">@if($p->image_url)<img src="{{ $p->image_url }}" class="h-10 w-10 object-cover rounded">@endif</td>
        <td class="p-2 border text-right">
          <a href="{{ route('admin.products.edit',$p) }}" class="px-3 py-1 border rounded">Editar</a>
          <form action="{{ route('admin.products.destroy',$p) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar producto?')">
            @csrf @method('DELETE')
            <button class="px-3 py-1 border rounded">Eliminar</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $products->links() }}</div>
@endsection
