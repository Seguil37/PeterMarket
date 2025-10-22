@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
  <h1 class="text-2xl font-bold mb-6">Panel de Administración</h1>
  @if(session('ok'))
    <div class="mb-4 rounded bg-green-100 p-3 text-green-800">{{ session('ok') }}</div>
  @endif
  @yield('admin-content')
</div>
@endsection
