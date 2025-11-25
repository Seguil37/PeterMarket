@extends('layouts.admin')
@section('admin-content')
  <div class="grid gap-4 md:grid-cols-2">
    <a href="{{ route('admin.products.index') }}" class="card-surface p-6 block card-hover">
      <h2 class="font-semibold mb-2 text-gray-900">Productos</h2>
      <p class="text-sm text-gray-600">Gestiona precios, stock e imágenes.</p>
    </a>
  </div>
@endsection
