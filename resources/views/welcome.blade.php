@extends('layouts.app')
@section('title','Productos')

@section('content')
  <section class="relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-100/40 via-white to-emerald-50/70"></div>
    <div class="absolute -left-20 -top-28 size-72 bg-gradient-to-br from-blue-500/20 via-teal-400/20 to-emerald-300/10 blur-3xl rounded-full"></div>
    <div class="absolute -right-12 top-10 size-64 bg-gradient-to-br from-indigo-400/20 via-sky-300/20 to-cyan-200/10 blur-3xl rounded-full"></div>

    <div class="section-shell relative pt-12 pb-6 lg:pt-16 lg:pb-10 grid gap-8 lg:grid-cols-[1.2fr,1fr] items-center">
      <div class="space-y-6">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 text-sm font-semibold border border-blue-100 shadow-sm">
          Nueva experiencia de compra
        </div>
        <div class="space-y-3">
          <p class="text-sm text-gray-600 flex items-center gap-2">
            <span class="inline-flex size-8 items-center justify-center rounded-full bg-white shadow-sm">🛍️</span>
            Catálogo curado de productos confiables
          </p>
          <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-gray-900">Tu mercado digital con estilo profesional</h1>
          <p class="text-lg text-gray-600 leading-relaxed">Explora productos frescos, selecciona tus favoritos y recibe tu compra sin complicaciones. Hemos rediseñado cada detalle para que disfrutes de un catálogo inspirador y fluido.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
          <a href="{{ route('catalog.index') }}" class="btn-gradient">Explorar catálogo</a>
          <a href="{{ route('about') }}" class="btn-soft">Conoce nuestra historia</a>
          <span class="badge-pill bg-blue-50 text-blue-700">{{ number_format($products->total()) }} productos</span>
        </div>
        <div class="grid gap-3 sm:grid-cols-3">
          <div class="stat-tile">
            <div class="size-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-semibold">24/7</div>
            <div>
              <div class="text-sm text-gray-500">Soporte</div>
              <div class="font-semibold text-gray-900">Siempre contigo</div>
            </div>
          </div>
          <div class="stat-tile">
            <div class="size-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-semibold">+{{ $products->count() }}</div>
            <div>
              <div class="text-sm text-gray-500">Opciones actuales</div>
              <div class="font-semibold text-gray-900">Disponibles hoy</div>
            </div>
          </div>
          <div class="stat-tile">
            <div class="size-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-semibold">★</div>
            <div>
              <div class="text-sm text-gray-500">Calidad curada</div>
              <div class="font-semibold text-gray-900">Listas para ti</div>
            </div>
          </div>
        </div>
      </div>

      <div class="relative">
        <div class="glass-panel p-6 shadow-2xl">
          <p class="text-sm font-semibold text-blue-700">Filtros inteligentes</p>
          <p class="text-2xl font-bold text-gray-900 leading-tight">Encuentra rápido lo que necesitas</p>
          <p class="mt-2 text-sm text-gray-600">Combina búsqueda, categorías y ordenamientos para obtener resultados precisos.</p>
          <form method="GET" action="{{ route('catalog.index') }}" class="mt-6 grid grid-cols-1 gap-4">
            <div>
              <label class="sr-only" for="q">Buscar</label>
              <input id="q" name="q" value="{{ $q ?? '' }}" placeholder="Buscar producto…"
                     class="w-full border border-slate-200 rounded-xl px-4 py-3 shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-200 bg-white" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="sr-only" for="category">Categoría</label>
                <select id="category" name="category" class="w-full border border-slate-200 rounded-xl px-4 py-3 shadow-inner bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
                  <option value="" @selected(($category ?? '')==='')>Todas las categorías</option>
                  @foreach($categories as $option)
                    <option value="{{ $option }}" @selected(($category ?? '')===$option)>{{ $option }}</option>
                  @endforeach
                </select>
              </div>
              <div>
                <label class="sr-only" for="sort">Ordenar por</label>
                <select id="sort" name="sort" class="w-full border border-slate-200 rounded-xl px-4 py-3 shadow-inner bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
                  <option value="name_asc"  @selected(($sort ?? '')==='name_asc')>Nombre (A–Z)</option>
                  <option value="price_asc" @selected(($sort ?? '')==='price_asc')>Precio (menor primero)</option>
                  <option value="price_desc"@selected(($sort ?? '')==='price_desc')>Precio (mayor primero)</option>
                  <option value="stock_desc"@selected(($sort ?? '')==='stock_desc')>Stock (mayor primero)</option>
                </select>
              </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
              <button class="btn-gradient flex-1 sm:flex-none">Aplicar filtros</button>
              @if(($q ?? '') !== '' || ($sort ?? '') !== 'name_asc' || ($category ?? '') !== '')
                <a href="{{ route('catalog.index') }}" class="btn-outline">Limpiar</a>
              @endif
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

  <div class="section-shell py-10">
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
          $excerpt = $product->description ?: 'Categoría: ' . $product->category_type;
        @endphp
        <article class="flex flex-col bg-white rounded-2xl border border-slate-100 shadow-lg overflow-hidden card-hover">
          <div class="relative">
            <img src="{{ $product->image_url }}" alt="Imagen de {{ $product->name }}"
                 class="w-full h-52 object-cover">
            <div class="absolute top-3 left-3 flex flex-wrap gap-2">
              <span class="badge bg-white/90 text-gray-800 border border-slate-200">{{ $product->category_type }}</span>
              <span class="badge-pill {{ $badge }}">{{ $label }}</span>
            </div>
          </div>
          <div class="p-5 flex flex-col gap-4 flex-1">
            <div class="space-y-2">
              <h2 class="text-xl font-semibold text-gray-900">{{ $product->name }}</h2>
              <p class="text-sm text-gray-600 leading-relaxed line-clamp-3">{{ $excerpt }}</p>
            </div>
            <div class="flex items-center justify-between">
              <div class="text-2xl font-bold text-gray-900">S/ {{ number_format($product->price, 2) }}</div>
              <a href="{{ route('catalog.show', $product) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:text-blue-800">
                Ver detalles
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                  <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 0 1 1.414 0l5 5a1 1 0 0 1 0 1.414l-5 5a1 1 0 0 1-1.414-1.414L13.586 11H4a1 1 0 1 1 0-2h9.586l-3.293-3.293a1 1 0 0 1 0-1.414Z" clip-rule="evenodd" />
                </svg>
              </a>
            </div>
            <div class="mt-auto flex flex-wrap items-center gap-3">
              <form method="POST" action="{{ route('cart.add') }}" class="flex items-center gap-3 flex-1">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <label class="sr-only" for="qty-{{ $product->id }}">Cantidad</label>
                <input id="qty-{{ $product->id }}" name="quantity" type="number" min="1" value="1"
                       class="w-24 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
                       {{ $stock <= 0 ? 'disabled' : '' }}>
                <button class="btn-primary disabled:opacity-50" {{ $stock <= 0 ? 'disabled' : '' }}>
                  Agregar al carrito
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
