@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 space-y-6">
  <div>
    <h1 class="text-2xl font-bold">Panel de Administración</h1>
    <p class="text-sm text-gray-500">Controla inventario, productos y cuentas de administradores.</p>
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
         class="px-4 py-2 rounded-full text-sm border {{ request()->routeIs($link['route'].'*') ? 'bg-black text-white border-black' : 'bg-white text-gray-700 border-gray-200 hover:border-gray-400' }}">
        {{ $link['label'] }}
      </a>
    @endforeach
  </nav>

  @if(session('ok'))
    <div class="rounded bg-green-100 p-3 text-green-800">{{ session('ok') }}</div>
  @endif

  @yield('admin-content')
</div>
@endsection
