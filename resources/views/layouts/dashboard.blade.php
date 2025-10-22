@extends('layouts.admin')
@section('admin-content')
  <div class="grid gap-4 md:grid-cols-2">
    <a href="{{ route('admin.products.index') }}" class="border rounded-xl p-6 block">
      <h2 class="font-semibold mb-2">Productos</h2>
      <p class="text-sm text-gray-600">Gestiona precios, stock e imágenes.</p>
    </a>
  </div>
@endsection
