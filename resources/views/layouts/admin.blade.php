@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-10 space-y-8">
  <div class="flex flex-col gap-2 card-surface p-6">
    <div class="flex items-center justify-between gap-3 flex-wrap">
      <div>
        <p class="text-xs uppercase tracking-[0.2em] text-blue-600 font-semibold">Panel de Administración</p>
        <h1 class="text-3xl font-bold text-gray-900">Control central de Peter Market</h1>
        <p class="text-sm text-gray-600">Controla inventario, productos y cuentas de administradores con una vista moderna.</p>
      </div>
      <div class="flex items-center gap-3 bg-blue-50 border border-blue-100 text-blue-700 px-4 py-3 rounded-xl shadow-inner">
        <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center text-lg font-bold shadow">⚡</div>
        <div class="text-sm leading-tight"><span class="font-semibold">Atajo rápido:</span> administra existencias y catálogos en segundos.</div>
      </div>
    </div>

    @php
      $adminLinks = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
        ['label' => 'Inventario', 'route' => 'admin.inventory.index'],
        ['label' => 'Productos', 'route' => 'admin.products.index'],
      ];
      if(auth()->user()?->is_master_admin) {
          $adminLinks[] = ['label' => 'Administradores', 'route' => 'admin.admins.index'];
      }
    @endphp

    <nav class="flex flex-wrap gap-3">
      @foreach($adminLinks as $link)
        <a href="{{ route($link['route']) }}"
           class="pill-nav {{ request()->routeIs($link['route'].'*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg' : 'bg-white text-gray-700 border border-gray-200 hover:border-gray-400' }}">
          {{ $link['label'] }}
        </a>
      @endforeach
    </nav>
  </div>

  @if(session('ok'))
    <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4 text-emerald-700 shadow-sm">{{ session('ok') }}</div>
  @endif

  <div class="card-surface p-6">
    @yield('admin-content')
  </div>
</div>
@endsection
