@extends('layouts.admin')
@section('admin-content')
  <h2 class="text-xl font-semibold mb-4">Editar: {{ $product->name }}</h2>
  @include('admin.products._form', [
    'route' => route('admin.products.update',$product),
    'method' => 'PUT',
    'product' => $product
  ])
@endsection
