@extends('layouts.app')
@section('title','Productos')

@section('content')
  <header class="border-b bg-gray-50/60">
    <form method="GET" action="{{ route('catalog.index') }}"
          class="max-w-7xl mx-auto px-4 py-4 grid grid-cols-1 md:grid-cols-12 gap-3">
      <div class="md:col-span-7">
        <label class="sr-only" for="q">Buscar</label>
        <input id="q" name="q" value="{{ $q ?? '' }}" placeholder="Buscar producto…"
               class="w-full border rounded-lg px-3 py-2" />
      </div>
      <div class="md:col-span-3">
        <label class="sr-only" for="sort">Ordenar por</label>
        <select id="sort" name="sort" class="w-full border rounded-lg px-3 py-2">
          <option value="name_asc"  @selected(($sort ?? '')==='name_asc')>Nombre (A–Z)</option>
          <option value="price_asc" @selected(($sort ?? '')==='price_asc')>Precio (menor primero)</option>
          <option value="price_desc"@selected(($sort ?? '')==='price_desc')>Precio (mayor primero)</option>
          <option value="stock_desc"@selected(($sort ?? '')==='stock_desc')>Stock (mayor primero)</option>
        </select>
      </div>
      <div class="md:col-span-2 flex gap-2">
        <button class="flex-1 px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-black">Aplicar</button>
        @if(($q ?? '') !== '' || ($sort ?? '') !== 'name_asc')
          <a href="{{ route('catalog.index') }}" class="px-4 py-2 rounded-lg border">Limpiar</a>
        @endif
      </div>
    </form>
  </header>

  <div class="max-w-7xl mx-auto px-4 py-6">
    @if($products->isEmpty())
      <div class="p-8 border rounded-lg bg-white text-center">
        <p class="text-lg">No encontramos productos para <strong>{{ $q }}</strong>.</p>
        <a href="{{ route('catalog.index') }}" class="mt-4 inline-block px-4 py-2 rounded-lg bg-blue-600 text-white">Ver todo</a>
      </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      @foreach ($products as $product)
        <article class="bg-white rounded-xl shadow-sm border hover:shadow-md transition">
          <img src="{{ $product->image_url }}" alt="Imagen de {{ $product->name }}"
               class="w-full h-44 object-cover rounded-t-xl">
          <div class="p-4 space-y-2">
            <h2 class="font-semibold">{{ $product->name }}</h2>
            <p class="text-xs text-gray-500">Categoría: {{ $product->category_type }}</p>
            <div class="text-sm text-gray-600">S/ {{ number_format($product->price, 2) }}</div>

            @php
              $stock = (int)$product->stock;
              $badge = $stock <= 0 ? 'bg-red-100 text-red-700' : ($stock <= 10 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700');
              $label = $stock <= 0 ? 'Sin stock' : "Stock: $stock";
            @endphp
            <span class="inline-block text-xs px-2 py-1 rounded {{ $badge }}">{{ $label }}</span>

            <a href="{{ route('catalog.show', $product) }}"
               class="mt-2 inline-flex w-full items-center justify-center gap-1 rounded-lg border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
              Ver más
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 0 1 1.414 0l5 5a1 1 0 0 1 0 1.414l-5 5a1 1 0 0 1-1.414-1.414L13.586 11H4a1 1 0 1 1 0-2h9.586l-3.293-3.293a1 1 0 0 1 0-1.414Z" clip-rule="evenodd" />
              </svg>
            </a>

            <form method="POST" action="{{ route('cart.add') }}" class="mt-3 flex items-center gap-2">
              @csrf
              <input type="hidden" name="product_id" value="{{ $product->id }}">
              <label class="sr-only" for="qty-{{ $product->id }}">Cantidad</label>
              <input id="qty-{{ $product->id }}" name="quantity" type="number" min="1" value="1"
                     class="w-20 border rounded-lg px-2 py-1" {{ $stock <= 0 ? 'disabled' : '' }}>
              <button class="px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50"
                      {{ $stock <= 0 ? 'disabled' : '' }}>
                Agregar
              </button>
            </form>
          </div>
        </article>
      @endforeach
    </div>

    <div class="mt-6">
      {{ $products->onEachSide(1)->links() }}
    </div>
  </div>
@endsection
