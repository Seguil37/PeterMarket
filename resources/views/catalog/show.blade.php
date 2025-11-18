@extends('layouts.app')
@section('title', $product->name)

@section('content')
  <div class="max-w-6xl mx-auto px-4 py-8 space-y-10">
    <nav class="text-sm text-gray-500 flex items-center gap-2">
      <a href="{{ route('catalog.index') }}" class="hover:text-gray-900">Inicio</a>
      <span>/</span>
      <span class="text-gray-900 font-medium">{{ $product->name }}</span>
    </nav>

    <div class="grid gap-8 md:grid-cols-2">
      <div class="bg-white rounded-2xl border overflow-hidden shadow-sm">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
             class="w-full h-96 object-cover">
      </div>

      <div class="space-y-6">
        <div>
          <p class="text-sm text-gray-500 uppercase tracking-wide">Producto</p>
          <h1 class="text-3xl font-semibold text-gray-900">{{ $product->name }}</h1>
        </div>

        <div class="text-4xl font-bold text-emerald-600">
          S/ {{ number_format($product->price, 2) }}
        </div>

        <div class="space-y-2 text-sm">
          <p class="text-gray-700">{{ $product->description ?: 'Este producto no cuenta con una descripción detallada todavía.' }}</p>
        </div>

        @php
          $stock = (int) $product->stock;
          $badge = $stock <= 0 ? 'bg-red-100 text-red-700' : ($stock <= 10 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700');
          $label = $stock <= 0 ? 'Sin stock disponible' : "Stock disponible: $stock unidades";
        @endphp
        <span class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm {{ $badge }}">
          {{ $label }}
        </span>

        <form method="POST" action="{{ route('cart.add') }}" class="space-y-4">
          @csrf
          <input type="hidden" name="product_id" value="{{ $product->id }}">
          <div>
            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
            <input id="quantity" name="quantity" type="number" min="1" value="1"
                   class="w-28 border rounded-lg px-3 py-2" {{ $stock <= 0 ? 'disabled' : '' }}>
          </div>
          <button class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-white text-lg font-semibold hover:bg-emerald-700 disabled:opacity-50"
                  {{ $stock <= 0 ? 'disabled' : '' }}>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 2.25h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3v.75h15v-.75a3 3 0 0 0-3-3m-9 0L5.106 4.522M7.5 14.25h9m0 0 2.394-9.728a1.125 1.125 0 0 0-1.088-1.397H4.72" />
            </svg>
            Añadir al carrito
          </button>
        </form>
      </div>
    </div>

    @if($recommended->isNotEmpty())
      <section>
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-semibold text-gray-900">Productos recomendados</h2>
          <span class="text-sm text-gray-500">Basado en lo que otros clientes están viendo</span>
        </div>
        <div class="flex gap-4 overflow-x-auto pb-4 snap-x">
          @foreach ($recommended as $item)
            <a href="{{ route('catalog.show', $item) }}" class="min-w-[220px] snap-center rounded-xl border bg-white shadow-sm hover:shadow-md transition">
              <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="h-40 w-full object-cover rounded-t-xl">
              <div class="p-4 space-y-1">
                <p class="text-sm text-gray-500">{{ $item->name }}</p>
                <p class="text-lg font-semibold text-gray-900">S/ {{ number_format($item->price, 2) }}</p>
              </div>
            </a>
          @endforeach
        </div>
      </section>
    @endif
  </div>
@endsection
