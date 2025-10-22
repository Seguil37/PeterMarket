@extends('layouts.app')
@section('title', 'Panel de administración')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">
  <h1 class="text-2xl font-semibold mb-6">Panel de administración</h1>

  <div class="grid md:grid-cols-3 gap-6">
    {{-- Inventario --}}
    <a href="{{ route('admin.inventory.index') }}"
       class="block rounded-2xl border p-5 bg-white hover:shadow transition">
      <div class="text-lg font-medium">Inventario</div>
      <div class="text-sm text-gray-500">Registrar compras y entradas de stock.</div>
    </a>

    {{-- Compras (lista) - opcional si la mantienes --}}
    <a href="{{ route('admin.inventory.index') }}"
       class="block rounded-2xl border p-5 bg-white hover:shadow transition">
      <div class="text-lg font-medium">Compras (lista)</div>
      <div class="text-sm text-gray-500">Ir directo al módulo de compras.</div>
    </a>

    {{-- Productos (CRUD admin) --}}
    <a href="{{ route('admin.products.index') }}"
       class="block rounded-2xl border p-5 bg-white hover:shadow transition">
      <div class="text-lg font-medium">Productos</div>
      <div class="text-sm text-gray-500">CRUD productos.</div>
    </a>
  </div>
</div>
@endsection
