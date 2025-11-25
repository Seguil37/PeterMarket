@extends('layouts.app')
@section('title','Productos')

@section('content')
  <header class="border-b bg-white/70 backdrop-blur">
    <form method="GET" action="{{ route('catalog.index') }}"
          class="max-w-7xl mx-auto px-4 py-5 grid grid-cols-1 md:grid-cols-12 gap-3 md:gap-4">
      <div class="md:col-span-5">
        <label class="sr-only" for="q">Buscar</label>
        <input id="q" name="q" value="{{ $q ?? '' }}" placeholder="Buscar producto…"
               class="w-full border border-slate-200 rounded-xl px-4 py-3 shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-200" />
      </div>
      <div class="md:col-span-3">
        <label class="sr-only" for="category">Categoría</label>
        <select id="category" name="category" class="w-full border border-slate-200 rounded-xl px-4 py-3 shadow-inner bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
          <option value="" @selected(($category ?? '')==='')>Todas las categorías</option>
          @foreach($categories as $option)
            <option value="{{ $option }}" @selected(($category ?? '')===$option)>{{ $option }}</option>
          @endforeach
        </select>
      </div>
      <div class="md:col-span-2">
        <label class="sr-only" for="sort">Ordenar por</label>
        <select id="sort" name="sort" class="w-full border border-slate-200 rounded-xl px-4 py-3 shadow-inner bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
          <option value="name_asc"  @selected(($sort ?? '')==='name_asc')>Nombre (A–Z)</option>
          <option value="price_asc" @selected(($sort ?? '')==='price_asc')>Precio (menor primero)</option>
          <option value="price_desc"@selected(($sort ?? '')==='price_desc')>Precio (mayor primero)</option>
          <option value="stock_desc"@selected(($sort ?? '')==='stock_desc')>Stock (mayor primero)</option>
        </select>
      </div>
      <div class="md:col-span-2 flex gap-2">
        <button class="flex-1 btn-primary">Aplicar</button>
        @if(($q ?? '') !== '' || ($sort ?? '') !== 'name_asc' || ($category ?? '') !== '')
          <a href="{{ route('catalog.index') }}" class="btn-outline">Limpiar</a>
        @endif
      </div>
    </form>
  </header>

  <div class="max-w-7xl mx-auto px-4 py-6">
    @if($products->isEmpty())
      <div class="p-10 card-surface text-center space-y-3">
        <div class="text-3xl">🛒</div>
        <p class="text-lg font-semibold">No encontramos productos para <strong>{{ $q }}</strong>.</p>
        <p class="text-sm text-gray-600">Revisa otros filtros o vuelve al catálogo completo.</p>
        <a href="{{ route('catalog.index') }}" class="btn-primary w-full sm:w-auto justify-center">Ver todo</a>
      </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
      @foreach ($products as $product)
        @php
          $stock = (int)$product->stock;
          $badge = $stock <= 0 ? 'bg-red-100 text-red-700' : ($stock <= 10 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700');
          $label = $stock <= 0 ? 'Sin stock' : "Stock: $stock";
          $excerpt = $product->description ? \Illuminate\Support\Str::limit($product->description, 90) : 'Categoría: ' . $product->category_type;
        @endphp
        <article class="flex flex-col bg-white rounded-2xl border border-slate-100 shadow-md overflow-hidden card-hover">
          <div class="relative">
            <img src="{{ $product->image_url }}" alt="Imagen de {{ $product->name }}"
                 class="w-full h-48 object-cover">
            <span class="absolute top-3 left-3 badge bg-white/90 text-gray-800 border border-slate-200">{{ $product->category_type }}</span>
          </div>
          <div class="p-5 flex flex-col gap-3 flex-1">
            <div class="space-y-1">
              <h2 class="text-lg font-semibold text-gray-900">{{ $product->name }}</h2>
              <p class="text-sm text-gray-600 leading-relaxed">{{ $excerpt }}</p>
            </div>
            <div class="flex items-center justify-between">
              <div class="text-base font-semibold text-gray-900">S/ {{ number_format($product->price, 2) }}</div>
              <span class="text-xs px-2 py-1 rounded {{ $badge }}">{{ $label }}</span>
            </div>
            <div class="mt-auto flex items-center justify-between gap-3 pt-2">
              <a href="{{ route('catalog.show', $product) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-blue-700 hover:text-blue-800 hover:underline underline-offset-4">
                Ver más
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                  <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 0 1 1.414 0l5 5a1 1 0 0 1 0 1.414l-5 5a1 1 0 0 1-1.414-1.414L13.586 11H4a1 1 0 1 1 0-2h9.586l-3.293-3.293a1 1 0 0 1 0-1.414Z" clip-rule="evenodd" />
                </svg>
              </a>
              <form method="POST" action="{{ route('cart.add') }}" class="flex items-center gap-2">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <label class="sr-only" for="qty-{{ $product->id }}">Cantidad</label>
                <input id="qty-{{ $product->id }}" name="quantity" type="number" min="1" value="1"
                       class="w-20 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
                       {{ $stock <= 0 ? 'disabled' : '' }}>
                <button class="btn-soft disabled:opacity-50" {{ $stock <= 0 ? 'disabled' : '' }}>
                  Agregar
                </button>
              </form>
            </div>
          </div>
        </article>
      @endforeach
    </div>

    <div class="mt-6">
      {{ $products->onEachSide(1)->links() }}
    </div>
  </div>
@endsection
