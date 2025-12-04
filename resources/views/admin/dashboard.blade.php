@extends('layouts.admin')
@section('admin-content')
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

    {{-- Pedidos y reportes --}}
    <a href="{{ route('admin.orders.index') }}"
       class="block rounded-2xl border p-5 bg-white hover:shadow transition">
      <div class="text-lg font-medium">Pedidos</div>
      <div class="text-sm text-gray-500">Dashboard de ventas, reportes y estados.</div>
    </a>

    {{-- Clientes --}}
    <a href="{{ route('admin.customers.index') }}"
       class="block rounded-2xl border p-5 bg-white hover:shadow transition">
      <div class="text-lg font-medium">Clientes</div>
      <div class="text-sm text-gray-500">Gestiona cuentas y revisa compras por usuario.</div>
    </a>

    {{-- Productos (CRUD admin) --}}
    <a href="{{ route('admin.products.index') }}"
       class="block rounded-2xl border p-5 bg-white hover:shadow transition">
      <div class="text-lg font-medium">Productos</div>
      <div class="text-sm text-gray-500">CRUD productos.</div>
    </a>

    @if(auth()->user()?->is_master_admin)
    <a href="{{ route('admin.admins.index') }}"
       class="block rounded-2xl border p-5 bg-white hover:shadow transition">
      <div class="text-lg font-medium">Administradores</div>
      <div class="text-sm text-gray-500">Gestiona cuentas master y operativas.</div>
    </a>
    @endif
  </div>
</div>
@endsection
